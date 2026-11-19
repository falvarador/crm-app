<script>
    (() => {
        let theme = 'system';
        try {
            const saved = localStorage.getItem('aura-theme');
            if (['system', 'light', 'dark'].includes(saved)) theme = saved;
        } catch (error) {
            console.warn('No se pudo leer la preferencia de tema.', error);
        }
        document.documentElement.dataset.theme = theme;
    })();
</script>