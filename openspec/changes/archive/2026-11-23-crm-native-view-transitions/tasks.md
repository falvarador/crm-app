# Tasks

## 1. Movimiento y navegación SSR

- [x] 1.1 Definir el movimiento breve del contenido y la retroalimentación en las capas y tokens CSS existentes, respetando la preferencia reducida; verificar las duraciones, que no haya contenido oculto sin JavaScript y que las reglas existentes de diálogos y toasts sigan funcionando.
- [x] 1.2 Activar View Transitions nativas para navegación SSR del mismo origen sin interceptar enlaces; verificar en navegador compatible la navegación entre módulos, atrás/adelante, ausencia de capturas duplicadas y la navegación normal sin soporte.

## 2. Cambios asíncronos de vista

- [x] 2.1 Integrar transiciones progresivas para cambios de vista y filtros iniciados por el usuario en `crm-html-ajax`, preservando eventos, carga, errores y vigencia; ampliar las pruebas Node para soporte, falta de soporte, movimiento reducido, interrupción y sustitución antes del callback.
- [x] 2.2 Integrar una única transición por lote de fragmentos diferidos en `app.js`, revalidando y confirmando o descartando el lote completo; añadir pruebas de lote vigente, fallido y sustituido, y comprobar que el commit no se duplique si la transición se omite o falla.

## 3. Accesibilidad y verificación integrada

- [x] 3.1 Revisar y ajustar únicamente el feedback existente de controles, diálogos y toasts donde el movimiento aporte claridad; verificar apertura/cierre, Escape, retorno del foco, teclado y movimiento reducido sin alterar contratos.
- [x] 3.2 Ejecutar las pruebas JavaScript afectadas, `php artisan test --compact tests/Feature/CrmUiTest.php` y `npm run build`; verificar los flujos de navegación y fragmentos en escritorio y móvil, temas claro y oscuro, movimiento reducido y los navegadores disponibles, registrando explícitamente cualquier limitación de verificación.

  Limitaciones: el navegador integrado (WebKit) siguió informando `no-preference` tras activar la preferencia del sistema; movimiento reducido se verificó mediante pruebas simuladas y revisión CSS, no con la preferencia real. No se verificó en otros navegadores.
