# Tasks

## 1. Diálogos y movimiento

- [x] 1.1 Corregir la cascada de transformación de modales y drawers; comprobar en navegador que los paneles de creación de contactos, empresas y oportunidades aparecen completos y que sus formularios son utilizables.
- [x] 1.2 Conservar o corregir cierre por Escape y fondo, contención y retorno del foco; verificar apertura y cierre mediante teclado, ratón y el fallback de `closedby`.
- [x] 1.3 Incorporar alternativas de movimiento reducido para diálogos, sidebar y tarjetas; comprobar con `prefers-reduced-motion` activo que el estado sigue siendo perceptible.

## 2. Preferencias y contraste

- [x] 2.1 Restaurar `aura-theme` antes del primer pintado en ambos layouts y manejar almacenamiento inaccesible; verificar valores existentes, inválidos y errores de almacenamiento sin romper otras interacciones.
- [x] 2.2 Añadir al selector las opciones `system`, `light` y `dark`, persistencia y sincronización con el SO; comprobar cambios de tema del SO en tiempo real, preferencia manual, retorno a `system` y navegación SSR.
- [x] 2.3 Ajustar tokens y estados de interacción sin alterar las capas existentes; medir en ambos temas contraste computado de texto normal ≥4.5:1 y límites e indicadores necesarios ≥3:1, incluidos campos, botones y badges.
- [x] 2.4 Revisar superficies y controles nativos, scrollbars, fechas, selectores y backdrop; comprobar ambos temas y ausencia de destellos claros en oscuro.

## 3. Navegación y formularios

- [x] 3.1 Sincronizar estado accesible y foco del menú móvil, incluido su cierre; verificar con teclado que los enlaces ocultos no reciban foco y que `aria-expanded` refleje la visibilidad.
- [x] 3.2 Corregir semántica y teclado de filtros y vistas segmentadas; verificar que la selección anunciada y el contenido coincidan tras cada cambio y que se mantengan las URLs y filtros.
- [x] 3.3 Mantener el estado de carga de fragmentos ligado a la solicitud vigente y mejorar el feedback de formularios; comprobar cancelación seguida de otra petición, envío duplicado, éxito y error.
- [x] 3.4 Hacer persistentes y anunciables las regiones de error y estabilizar su espacio; verificar errores de cliente y servidor, asociación al campo, foco y ausencia de saltos bruscos.

## 4. Densidad y responsividad

- [x] 4.1 Revisar tablas y tarjetas Kanban para cifras tabulares, jerarquía y contenido largo; verificar nombres extensos, importes, scroll accesible y cambio de etapa sin arrastrar.
- [x] 4.2 Añadir acciones pertinentes a los estados vacíos reutilizando controles existentes; verificar listados sin registros, filtros sin resultados y tareas pendientes vacías.
- [x] 4.3 Ajustar áreas táctiles y contenedores estrechos; verificar objetivos principales de al menos 44 × 44 píxeles CSS o alternativa equivalente, y ausencia de pérdida de contenido en móvil.

## 5. Integración

- [x] 5.1 Ejecutar las pruebas relevantes existentes y comprobar en navegador los flujos de creación, filtrado y Kanban en ambos temas y tamaños de pantalla.
- [x] 5.2 Verificar teclado, lector de pantalla y movimiento reducido, y documentar cualquier incompatibilidad de navegador observada antes de dar el cambio por completo.
