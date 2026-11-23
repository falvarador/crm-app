import assert from 'node:assert/strict';
import test from 'node:test';

class Element extends EventTarget {
    constructor() {
        super();
        this.attributes = new Map();
        this.innerHTML = '';
    }

    getAttribute(name) {
        return this.attributes.get(name) ?? null;
    }

    setAttribute(name, value) {
        this.attributes.set(name, value);
    }

    removeAttribute(name) {
        this.attributes.delete(name);
    }
}

globalThis.HTMLElement = Element;
globalThis.window = new EventTarget();
globalThis.window.matchMedia = () => ({ matches: false });
globalThis.document = {};
let CrmHtmlAjax;
globalThis.customElements = {
    get: () => undefined,
    define: (_name, component) => { CrmHtmlAjax = component; },
};
await import('../resources/js/components/crm-html-ajax.js');
const { commitFragmentBatch, transitionView } = await import('../resources/js/view-transitions.js');

function deferTransition(t) {
    let update;
    let resolveUpdate;
    const updateCallbackDone = new Promise((resolve) => { resolveUpdate = resolve; });
    document.startViewTransition = (callback) => {
        update = callback;
        return { ready: Promise.resolve(), finished: Promise.resolve(), updateCallbackDone };
    };
    t.after(() => { delete document.startViewTransition; });

    return () => resolveUpdate(update());
}

test('a cancelled request cannot clear the current request busy state', async (t) => {
    const originalFetch = globalThis.fetch;
    t.after(() => { globalThis.fetch = originalFetch; });
    const pending = [];
    globalThis.fetch = (_url, { signal }) => new Promise((resolve, reject) => {
        pending.push(resolve);
        signal.addEventListener('abort', () => reject(new DOMException('Aborted', 'AbortError')));
    });
    const fragment = new CrmHtmlAjax();
    fragment.setAttribute('fragment', 'contacts-table');

    const first = fragment.reload('/first');
    const second = fragment.reload('/second');
    await first;
    assert.equal(fragment.getAttribute('aria-busy'), 'true');
    pending[1](new Response('latest', { status: 200 }));
    await second;

    assert.equal(fragment.getAttribute('aria-busy'), null);
    assert.equal(fragment.getAttribute('url'), '/second');
    assert.equal(fragment.innerHTML, 'latest');
});

test('a deferred fragment remains unchanged until committed and can be discarded', async (t) => {
    const originalFetch = globalThis.fetch;
    t.after(() => { globalThis.fetch = originalFetch; });
    globalThis.fetch = async () => new Response('updated', { status: 200 });
    const fragment = new CrmHtmlAjax();
    fragment.innerHTML = 'original';
    fragment.setAttribute('url', '/original');

    const discarded = await fragment.reload('/discarded', { deferRender: true });
    assert.equal(fragment.innerHTML, 'original');
    assert.equal(fragment.getAttribute('aria-busy'), 'true');
    discarded.discard();
    assert.equal(fragment.getAttribute('url'), '/original');
    assert.equal(fragment.getAttribute('aria-busy'), null);

    const committed = await fragment.reload('/updated', { deferRender: true });
    committed.commit();
    assert.equal(fragment.innerHTML, 'updated');
    assert.equal(fragment.getAttribute('url'), '/updated');
    assert.equal(fragment.getAttribute('aria-busy'), null);
});

test('a superseded deferred response cannot overwrite a newer response', async (t) => {
    const originalFetch = globalThis.fetch;
    t.after(() => { globalThis.fetch = originalFetch; });
    globalThis.fetch = async (url) => new Response(url, { status: 200 });
    const fragment = new CrmHtmlAjax();

    const stale = await fragment.reload('/stale', { deferRender: true });
    await fragment.reload('/latest');
    stale.commit();

    assert.equal(stale.isCurrent(), false);
    assert.equal(fragment.innerHTML, '/latest');
    assert.equal(fragment.getAttribute('url'), '/latest');
});

test('a user-initiated refresh waits for the transition callback and preserves busy state', async (t) => {
    const originalFetch = globalThis.fetch;
    t.after(() => { globalThis.fetch = originalFetch; });
    globalThis.fetch = async () => new Response('updated', { status: 200 });
    const runUpdate = deferTransition(t);
    const fragment = new CrmHtmlAjax();
    fragment.innerHTML = 'original';

    const request = fragment.reload('/updated', { animate: true });
    await new Promise((resolve) => setTimeout(resolve, 0));
    assert.equal(fragment.innerHTML, 'original');
    assert.equal(fragment.getAttribute('aria-busy'), 'true');
    runUpdate();
    await request;
    assert.equal(fragment.innerHTML, 'updated');
    assert.equal(fragment.getAttribute('aria-busy'), null);
});

test('an old transition callback cannot commit over a newer refresh', async (t) => {
    const originalFetch = globalThis.fetch;
    t.after(() => { globalThis.fetch = originalFetch; });
    globalThis.fetch = async (url) => new Response(url, { status: 200 });
    const runUpdate = deferTransition(t);
    const fragment = new CrmHtmlAjax();

    const stale = fragment.reload('/stale', { animate: true });
    await new Promise((resolve) => setTimeout(resolve, 0));
    await fragment.reload('/latest');
    runUpdate();
    await stale;

    assert.equal(fragment.innerHTML, '/latest');
    assert.equal(fragment.getAttribute('url'), '/latest');
    assert.equal(fragment.getAttribute('aria-busy'), null);
});

test('unsupported and reduced-motion transitions update immediately', async (t) => {
    let updates = 0;
    await transitionView(() => { updates++; });

    document.startViewTransition = () => { throw new Error('must not start'); };
    window.matchMedia = () => ({ matches: true });
    t.after(() => {
        delete document.startViewTransition;
        window.matchMedia = () => ({ matches: false });
    });
    await transitionView(() => { updates++; });
    assert.equal(updates, 2);
});

test('a skipped or failed transition does not repeat the update', async (t) => {
    const originalWarn = console.warn;
    console.warn = () => {};
    t.after(() => {
        console.warn = originalWarn;
        delete document.startViewTransition;
    });
    let updates = 0;
    document.startViewTransition = () => { throw new Error('unavailable'); };
    await transitionView(() => { updates++; });

    document.startViewTransition = (update) => {
        update();
        return {
            ready: Promise.reject(new DOMException('Skipped', 'AbortError')),
            finished: Promise.resolve(),
            updateCallbackDone: Promise.resolve(),
        };
    };
    await transitionView(() => { updates++; });

    document.startViewTransition = () => ({
        ready: Promise.reject(new DOMException('Skipped', 'AbortError')),
        finished: Promise.resolve(),
        updateCallbackDone: Promise.reject(new Error('callback not invoked')),
    });
    await transitionView(() => { updates++; });
    assert.equal(updates, 3);
});

test('a fragment batch is committed together inside one transition', async (t) => {
    const runUpdate = deferTransition(t);
    const committed = [];
    const results = [1, 2].map((id) => ({
        isCurrent: () => true,
        commit: () => committed.push(id),
        discard: () => assert.fail('current batch must not be discarded'),
    }));

    const batch = commitFragmentBatch(results, 2);
    assert.deepEqual(committed, []);
    runUpdate();
    await batch;
    assert.deepEqual(committed, [1, 2]);
});

test('a failed or superseded batch discards every available fragment', async (t) => {
    const discarded = [];
    let current = true;
    const results = [1, 2].map((id) => ({
        isCurrent: () => current,
        commit: () => assert.fail('stale batch must not commit'),
        discard: () => discarded.push(id),
    }));
    await commitFragmentBatch([results[0], undefined], 2);
    assert.deepEqual(discarded, [1]);

    discarded.length = 0;
    const runUpdate = deferTransition(t);
    const batch = commitFragmentBatch(results, 2);
    current = false;
    runUpdate();
    assert.equal(await batch, false);
    assert.deepEqual(discarded, [1, 2]);
});

test('a fragment update error is reported to the caller and releases the batch', async () => {
    const discarded = [];
    const error = new Error('render failed');
    const results = [1, 2].map((id) => ({
        isCurrent: () => true,
        commit: () => { throw error; },
        discard: () => discarded.push(id),
    }));

    await assert.rejects(commitFragmentBatch(results, 2), error);
    assert.deepEqual(discarded, [1, 2]);
});

test('a synchronous transition callback error is not hidden or retried', async (t) => {
    const error = new Error('render failed');
    let updates = 0;
    document.startViewTransition = (update) => update();
    t.after(() => { delete document.startViewTransition; });

    await assert.rejects(transitionView(() => {
        updates++;
        throw error;
    }), error);
    assert.equal(updates, 1);
});
