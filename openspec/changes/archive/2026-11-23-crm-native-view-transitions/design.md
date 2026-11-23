# Design

## Context

Ver `proposal.md` y el delta de `accessible-crm-interactions`. El layout compartido renderiza navegación SSR; `crm-html-ajax` sustituye contenido tras verificar la solicitud vigente y soporta commits diferidos. `app.js` coordina varios fragmentos para aplicarlos juntos. Los diálogos y toasts ya tienen transiciones CSS y existen tokens de 150 ms y 220 ms y reglas parciales para movimiento reducido.

## Goals / Non-Goals

**Goals:**

- Crear una sensación de continuidad breve y coherente sin introducir esperas en tareas repetidas.
- Mantener SSR, enlaces normales, estados accesibles y contratos de los componentes.
- Cubrir soporte parcial del navegador, interrupciones y movimiento reducido.

**Non-Goals:**

- Convertir el CRM en SPA, interceptar toda navegación, cambiar rutas o introducir dependencias.
- Animar cada recarga de datos de fondo, crear secuencias de carga o modificar las acciones comerciales.
- Rediseñar el tema ni reemplazar los mecanismos existentes de diálogos.

## Decisions

1. **Lenguaje de movimiento para una herramienta de trabajo.** La continuidad del área de contenido entre módulos y vistas es el momento principal: un fundido contenido, sin desplazamientos grandes. Usar los tokens existentes o ampliarlos solo si hace falta; apuntar a 100–150 ms para feedback, 150–300 ms para cambios de contenido y no superar 300 ms para navegación habitual. Diálogos, toasts y estados interactivos conservan sus transiciones, afinadas solo cuando exista una mejora clara. Alternativa descartada: entradas escalonadas y animaciones de scroll en cada página, que penalizan tareas repetitivas.

2. **Navegación SSR mediante opt-in CSS nativo.** Usar `@view-transition` con `navigation: auto` para navegaciones del mismo origen compatibles, sin interceptar enlaces ni cambiar historial, redirecciones o formularios. Aislar el contenido principal y mantener estable el shell solo si los nombres de transición son únicos en ambos documentos; comprobar visualmente que no aparezcan duplicaciones. Si no se admite, la navegación SSR sigue intacta. Alternativa descartada: emular navegación con fetch o convertirla en SPA.

3. **Cambios de vista dentro del documento.** Usar `document.startViewTransition` con detección de soporte en cambios de vista y filtros iniciados por el usuario, no en cada evento de actualización en segundo plano. En `crm-html-ajax`, conservar la comprobación de vigencia también dentro del callback de actualización, pues este puede ejecutarse después de que otra solicitud lo reemplace. Para commits diferidos coordinados en `app.js`, envolver el lote completo en una sola transición, volver a comprobar la vigencia del lote al ejecutarlo y descartar el lote si dejó de ser válido; nunca animar commits individuales del mismo lote. Mantener el comportamiento de `aria-busy`, eventos, errores y commit/discard; asegurar que cada mutación válida se ejecute una sola vez, incluso si se omite o falla la transición. Alternativa descartada: una transición por fragmento que podría romper la atomicidad visual.

4. **Preferencias, foco y degradación progresiva.** Evitar iniciar transiciones de documento cuando `prefers-reduced-motion: reduce` esté activo y desactivar los pseudo-elementos de transiciones de navegación y el movimiento no esencial por CSS. Respetar cambios de preferencia mientras la página está abierta. No retrasar el DOM, la limpieza de carga, los eventos, el foco o el cierre de diálogos hasta que concluya la animación. Una transición interrumpida no debe impedir el commit válido ni repetirlo; las excepciones reales del código de actualización deben seguir siendo visibles por los canales existentes. Alternativa descartada: ocultar fallos o asumir que todos los navegadores implementan la API.

5. **Presupuesto y comprobación.** Preferir opacidad y transformaciones pequeñas; no incorporar filtros costosos, bucles ni animar toda la lista por elemento. Probar escritorio y móvil en claro y oscuro, teclado, cambios rápidos y movimiento reducido; comprobar la compatibilidad cruzada donde haya navegadores disponibles. Las pruebas automatizadas deben cubrir actualización inmediata, diferida, cancelación, falta de soporte y preferencia reducida.

## Risks / Trade-offs

- [Capturas duplicadas o nombres repetidos] → limitar superficies nombradas y revisar ambas páginas y vistas antes de aplicar nombres compartidos.
- [Una nueva solicitud llega antes del callback de transición] → volver a validar la vigencia en el momento del commit, incluido el lote completo.
- [Una transición omitida o rechazada deja la interfaz ocupada] → aplicar o descartar exactamente una vez y limpiar estado mediante el flujo existente.
- [Cambios de enfoque por sustitución de fragmentos] → verificar teclado, foco y anuncios; no añadir movimientos de foco indiscriminados.
- [Soporte desigual de transiciones entre documentos] → mantener SSR funcional sin polyfills; comprobar soporte real y documentar las limitaciones observadas.

## Migration Plan

No hay migración de datos. Incorporar estilos y comportamiento de forma progresiva; ante una regresión, retirar el opt-in y el envoltorio de transición conservando el flujo original de navegación y commits.
