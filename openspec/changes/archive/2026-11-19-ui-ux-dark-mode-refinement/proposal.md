# Proposal

## Why

El CRM necesita una experiencia coherente y accesible durante jornadas de trabajo prolongadas. El diagnóstico identificó riesgos en los diálogos de creación, el contraste, la restauración del tema y la navegación y retroalimentación accesibles.

## What Changes

- Respetar el tema del sistema por defecto y ofrecer preferencias persistentes `system`, `light` y `dark`, restauradas antes del primer pintado en los layouts SSR.
- Refinar los tokens semánticos y los estados visuales para conseguir el contraste requerido y mantener superficies y controles nativos coherentes en ambos temas.
- Corregir la apertura de diálogos y mejorar el foco, la navegación móvil, las pestañas, los errores, las cargas y el respeto por la reducción de movimiento.
- Mejorar la legibilidad y el uso de tablas, tarjetas Kanban, estados vacíos y controles táctiles sin cambiar los flujos comerciales existentes.
- Validar las interacciones reales, el contraste y la responsividad sin introducir frameworks CSS o JavaScript.

## Capabilities

### New Capabilities

- `appearance-preferences`: Preferencias de tema del sistema y manuales, persistencia, primer pintado y contraste visual.
- `accessible-crm-interactions`: Interacciones accesibles y responsivas del CRM, incluidos diálogos, navegación, formularios, cargas, tablas y Kanban.

### Modified Capabilities

Ninguna: no hay especificaciones principales existentes.

## Impact

- Afecta a `resources/css/`, los layouts y componentes Blade, y los Web Components y lógica existente en `resources/js/`.
- Mantiene Laravel Blade SSR, Light DOM, CSS nativo, capas existentes, rutas y contratos de eventos y atributos semánticos.
- No añade dependencias ni cambia las reglas comerciales o las API del CRM.
