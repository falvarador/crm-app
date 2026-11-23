# Design

## Context

Ver `proposal.md` para la motivación. `resources/css/tokens.css` ya define `light-dark()` y `color-scheme`; `resources/js/app.js` restaura únicamente `light` o `dark` al cargar el DOM. Ambos layouts Blade cargan el mismo bundle. Los componentes reutilizables ya usan `<dialog>`, `aria-busy`, `aria-invalid`, mensajes asociados a campos y eventos de actualización.

El diagnóstico estático detectó que las reglas posteriores de variantes del diálogo podrían anular su transformación abierta, que los bordes de campos no alcanzan 3:1 frente al fondo y que los filtros con `role="tab"` no actualizan `aria-selected`. Estos resultados deben verificarse con estilos computados y navegación real.

## Goals / Non-Goals

**Goals:**

- Reutilizar tokens, capas y componentes existentes para cumplir las especificaciones sin cambiar los flujos comerciales.
- Verificar estados reales en ambos temas, pantallas estrechas y navegación por teclado.

**Non-Goals:**

- Rediseñar la identidad visual o cambiar rutas, API, modelos y eventos de negocio.
- Introducir frameworks CSS/JS, dependencias o un componente de tema nuevo si el selector existente es suficiente.
- Considerar la vista de bienvenida no enlazada como parte del CRM activo sin comprobar antes su uso.

## Decisions

### Tema como preferencia, no como color efectivo

Conservar `aura-theme` para compatibilidad con las preferencias `light` y `dark` ya guardadas. Representar explícitamente `system` y tratar valores inválidos como `system`. Un micro-script compartido en el `<head>` de ambos layouts restaura exclusivamente valores permitidos antes de los estilos que pintan la página; si el almacenamiento falla, continúa con `system` sin bloquear la inicialización de otros componentes. El CSS mantiene `color-scheme: light dark` en el estado de sistema y usa `only light` o `only dark` para las elecciones manuales.

En `system`, `light-dark()` sigue al SO de forma nativa; un listener de `matchMedia` mantiene sincronizada cualquier información de tema efectivo expuesta por la interfaz, sin convertir la preferencia `system` en una selección manual. Actualizar los estados accesibles del selector y limpiar listeners si corresponde. Esto evita depender exclusivamente de JavaScript para pintar el tema, a diferencia de aplicar clases oscuras calculadas tras `DOMContentLoaded`.

### Tokens y medición de contraste

Mantener `@layer` y los nombres semánticos existentes; ajustar tokens compartidos y variantes de interacción antes de añadir excepciones locales. Medir combinaciones de colores computadas, incluidos bordes significativos, texto secundario, badges, fondos mezclados y estados hover, focus y active, sin exigir 3:1 a bordes meramente decorativos. Evitar negro puro en superficies y revisar nativos con `color-scheme`, incluyendo backdrop y scrollbars.

### Diálogos y navegación

Corregir la cascada para que las transformaciones finales abiertas de modal y drawer ganen a las transformaciones iniciales. Conservar `showModal()`, su contención nativa del foco y Escape, el cierre en fondo existente y el retorno de foco; probar el comportamiento en navegadores soportados antes de introducir lógica adicional. Reducir las transiciones según `prefers-reduced-motion`.

Para navegación móvil, sincronizar `aria-expanded` y accesibilidad de la barra oculta, incluidos foco y cierre. Para selectores declarados como pestañas, sincronizar `aria-selected` y navegación de teclado con el panel o filtro mostrado; si no representan paneles de pestañas, usar una semántica de botones apropiada. La solución debe preservar los filtros y sus URLs actuales.

### Retroalimentación y contenido adaptable

Mantener los contratos de `crm-form-ajax`, `crm-html-ajax`, `crm-form-validate` y sus eventos. Vincular el estado de carga a la solicitud vigente, conservar el tamaño de los controles durante el envío y comunicar el progreso sin bloquear innecesariamente el teclado. Dar a los mensajes una región viva persistente y reservar espacio razonable para errores. Reutilizar el slot de acción vacía de tablas y los controles de creación existentes; mantener el selector de etapa accesible como alternativa al arrastre.

Usar truncado donde no oculte información necesaria, scroll accesible para tablas y tamaños táctiles adecuados a la pantalla; preservar cifras tabulares existentes. No animar cambios de disposición como norma y conservar una señal de estado visible cuando se reduzca el movimiento.

## Risks / Trade-offs

- **[Cascada y soporte CSS]** → Probar estilos computados y transiciones en los navegadores soportados; mantener fallback funcional si no admiten transiciones discretas.
- **[Select nativo en WebKit]** → En la vista previa de macOS, `appearance: auto` limita la altura del control aunque se indique un mínimo de 44 px. En pantallas estrechas o táctiles se usa `appearance: none` con indicador CSS, conservando el selector y su comportamiento nativo.
- **[FOUC y almacenamiento inaccesible]** → Ejecutar la restauración antes del pintado y manejar el fallo del almacenamiento sin impedir el resto de la página.
- **[Cambios de foco o semántica]** → Verificar apertura, cierre, navegación y anuncios con teclado y tecnología asistiva; evitar duplicar mecanismos nativos.
- **[Contraste dependiente del contexto]** → Medir colores finales sobre el fondo real, no solo comparar luminosidad de tokens.
- **[Solicitudes concurrentes]** → Probar cancelaciones y respuestas sucesivas para no retirar el estado ocupado de la petición vigente.

## Migration Plan

Desplegar CSS, Blade y JS conjuntamente; no hay migración de base de datos. Conservar la clave y valores manuales existentes. Para revertir, desplegar conjuntamente la versión anterior de esos recursos; `system` no se interpretará como tema manual en la versión anterior.
