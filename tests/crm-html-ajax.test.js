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
let CrmHtmlAjax;
globalThis.customElements = {
    get: () => undefined,
    define: (_name, component) => { CrmHtmlAjax = component; },
};
await import('../resources/js/components/crm-html-ajax.js');

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
