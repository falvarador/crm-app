---
trigger: always_on
---

# Reglas de Arquitectura del CRM (Laravel + Light DOM + CSS Moderno)

1. **Traducción de Google Stitch:** Los archivos en `_design_refs/stitch/` son referencias visuales y estructurales. NUNCA copies su CSS inline ni clases utilitarias crudas directamente. Debes extraer su jerarquía visual, espaciados y paleta, y traducirlos a:
   - Componentes Blade semánticos (`<x-ui.*>`).
   - CSS nativo moderno organizado en `@layer reset, tokens, base, layout, components, utilities;` con variables en `oklch()`, `light-dark()`, CSS Nesting nativo (`&`), `:has()`, `@container` y `@starting-style`.
2. **Interactividad:** Prohibido usar React, Vue, Alpine.js o Livewire. Toda interactividad debe envolver el HTML de Stitch usando nuestros Light DOM Web Components (`<crm-form-validate>`, `<crm-form-ajax>`, `<crm-html-ajax>`, `<crm-dialog>`, `<crm-toast>`, `<crm-kanban>`).
3. **SSR Híbrido:** Las tablas y listas deben usar directivas `@fragment` de Blade para responder a peticiones con cabecera `X-Fragment-Request: true`.