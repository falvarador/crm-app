export async function transitionView(update) {
    if (typeof document.startViewTransition !== 'function' ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return update();
    }

    let updated = false;
    const updateOnce = () => {
        if (updated) return;
        updated = true;
        return update();
    };

    let transition;
    try {
        transition = document.startViewTransition(updateOnce);
    } catch (error) {
        if (updated) throw error;
        console.warn('No se pudo iniciar la transición de vista.', error);
        return updateOnce();
    }

    transition.ready.catch((error) => {
        if (error.name !== 'AbortError') {
            console.warn('No se pudo mostrar la transición de vista.', error);
        }
    });
    transition.finished.catch((error) => {
        if (error.name !== 'AbortError') {
            console.warn('No se pudo completar la transición de vista.', error);
        }
    });

    try {
        return await transition.updateCallbackDone;
    } catch (error) {
        if (updated) throw error;
        console.warn('No se pudo aplicar la transición de vista.', error);
        return updateOnce();
    }
}

export async function commitFragmentBatch(results, expectedCount) {
    const isCurrent = () => results.length === expectedCount && results.every((result) => result?.isCurrent());
    const discard = () => results.forEach((result) => result?.discard());

    if (!isCurrent()) {
        discard();
        return false;
    }

    let committed = false;
    try {
        await transitionView(() => {
            if (!isCurrent()) {
                discard();
                return;
            }
            results.forEach((result) => result.commit());
            committed = true;
        });
    } catch (error) {
        discard();
        throw error;
    }

    return committed;
}
