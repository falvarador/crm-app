# Proposal

## Why

La interfaz conserva controles que duplican acciones, sugieren funciones que no existen o muestran información repetida. Esta limpieza hará más clara la navegación y más escaneables las vistas densas sin cambiar los flujos comerciales existentes ni la arquitectura nativa de Blade, CSS y Web Components.

## What Changes

- Eliminar o corregir los controles y textos engañosos o redundantes enumerados abajo; conservar los controles que ya tienen una acción útil y accesible.
- Unificar el control para completar/reabrir tareas del dashboard, haciendo explícito el texto de la acción.
- Preseleccionar en el formulario de oportunidad la etapa correspondiente a la columna Kanban desde la que se inicia la creación.
- Retirar la vista de bienvenida de Laravel, que no tiene referencias de vista ni una ruta activa en el CRM.
- Tras aprobar y aplicar la limpieza de Blade, retirar únicamente reglas CSS o código JS cuya ausencia de uso se verifique en todas las vistas y en los estados dinámicos.
- No añadir dependencias ni frameworks, ni alterar los contratos de `<crm-form-ajax>`, `<crm-html-ajax>`, `<crm-dialog>` o `<crm-toast>`.

### Inventario de Fase 1 (propuestas; no ejecutadas)

| Archivo | Elemento detectado | Motivo | Acción propuesta |
|---|---|---|---|
| `resources/views/components/ui/layout.blade.php` | Botón «Aura Workspace» con dominio fijo `aura.attio.io`; abre la paleta de navegación, no un selector de espacio de trabajo. | Dato de demostración y acción con semántica engañosa. | Quitar el dominio y sustituir el botón por identidad estática del CRM. |
| `resources/views/components/ui/layout.blade.php` | Campana «Actividades pendientes» navega a todas las actividades, sin filtro ni contador; el módulo ya está en el sidebar. | Icono/etiqueta de notificación no respaldados y navegación duplicada. | Eliminar la campana; conservar el enlace real del sidebar. |
| `resources/views/components/ui/layout.blade.php` | Botón «Atajos y ayuda» abre la misma paleta que el buscador y no muestra ayuda. | Control duplicado y destino que no corresponde con la etiqueta. | Eliminarlo; conservar el buscador y su acceso por teclado. |
| `resources/views/components/ui/layout.blade.php` | Etiqueta «Espacios» encabeza enlaces a módulos del CRM. | Etiqueta imprecisa, no representa la navegación visible. | Cambiarla por «CRM». |
| `resources/views/components/ui/layout.blade.php` | Botón «Cerrar (ESC)» en el pie de la paleta, además del botón de cierre del diálogo y Escape. | Control redundante. | Eliminar el botón del pie y mantener el cierre accesible del diálogo. |
| `resources/views/modules/dashboard/index.blade.php` | «Operaciones en Vivo» y «seguimiento en tiempo real». | Afirmaciones de tiempo real no respaldadas por la actualización por eventos locales observada. | Eliminar las afirmaciones; no presentar el refresco local como tiempo real. |
| `resources/views/modules/dashboard/index.blade.php` | «Ver Pipeline» usa el icono `download` aunque navega a Oportunidades. | Icono que sugiere exportación y no la acción ejecutada. | Sustituirlo por un icono de navegación/pipeline. |
| `resources/views/modules/dashboard/index.blade.php` | Tareas pendientes tienen un botón iconográfico y otro botón «Completar» que envían el mismo `PATCH`; las completadas conservan el control de reapertura. | Acción duplicada para el mismo cambio de estado. | Unificar en un solo botón textual que alterne entre «Completar» y «Reabrir». |
| `resources/views/modules/dashboard/index.blade.php` | La cantidad de oportunidades próximas a cerrar aparece en el subtítulo y de nuevo en el pie; el enlace «Abrir tablero Kanban completo →» añade una flecha redundante. | Conteo y adorno repetidos. | Mantener una sola presentación del conteo y quitar la flecha decorativa. |
| `resources/views/modules/dashboard/index.blade.php` | Avatares con inicial junto al nombre de empresa o propietario en la tabla. | Iniciales repetidas en filas densas sin información adicional. | Retirar los avatares de esas filas y conservar los nombres. |
| `resources/views/modules/activities/index.blade.php` | «Gestión de Pipeline» y «Tiempo Real» en el encabezado. | Etiqueta ajena al propósito principal y afirmación de actualización no respaldada. | Eliminar el eyebrow; conservar el título y la descripción útil. |
| `resources/views/modules/activities/index.blade.php` | Badges superiores y tarjeta «Resumen de Actividades» repiten pendientes, vencidas y completadas. | Métricas duplicadas en la misma vista. | Conservar los badges superiores y retirar la tarjeta resumen duplicada. |
| `resources/views/modules/contacts/index.blade.php` | «Limpiar» aparece aunque no haya filtros; «Ver oportunidades» abre el pipeline general, no oportunidades del contacto. | Acción de reset sin efecto y acción contextual engañosa. | Mostrar «Limpiar» solo con filtros activos; eliminar «Ver oportunidades» mientras no exista un destino filtrado por contacto. |
| `resources/views/modules/contacts/index.blade.php` | Iniciales junto al nombre en cada fila; total de contactos en cabecera y pie; nota «Actualización parcial vía SSR Híbrido». | Iniciales y total redundantes; detalle técnico no útil para el usuario. | Quitar iniciales de la tabla y la nota técnica; conservar el conteo del pie con resultados/total. |
| `resources/views/modules/companies/index.blade.php` | Icono `filter_list` junto a «Filtros»; botón «Limpiar» incondicional; total en la cabecera y de nuevo en la barra de filtros. | Icono redundante y controles/conteos repetidos. | Quitar el icono y el badge de total de cabecera; mostrar «Limpiar» solo con filtros activos. |
| `resources/views/modules/companies/index.blade.php` | Iniciales repetidas en filas y contactos vinculados; iconos `domain` en pestañas y junto al nombre de empresa; iconos junto a metadatos ya rotulados de industria, ubicación y teléfono. | Adornos repetidos o iconos que repiten una etiqueta explícita. | Retirar esos elementos repetitivos; conservar el avatar único de identidad de la cuenta y el icono del enlace web externo. |
| `resources/views/modules/companies/index.blade.php` | «Ver oportunidades» abre el pipeline general, no oportunidades de la empresa activa. | Acción contextual que lleva a un listado sin filtrar. | Eliminarla mientras no exista una ruta/listado filtrado por empresa. |
| `resources/views/modules/deals/index.blade.php` | Botón «Añadir oportunidad» en cada columna abre el mismo formulario, cuyo valor inicial de etapa es «Prospecto». | El contexto de la columna se pierde al abrir el formulario. | Conectar cada botón para preseleccionar su etapa; si no se implementa esa conexión, retirar los botones por columna y conservar «Nueva Oportunidad». |
| `resources/views/modules/deals/index.blade.php` | Iniciales del propietario en las filas de tabla junto al nombre completo. | Repetición visual en el listado tabular. | Retirar las iniciales de esas filas; conservar el indicador de propietario en las tarjetas Kanban, donde es la única identificación visible. |
| `resources/views/welcome.blade.php` | Página de bienvenida de Laravel, con contenido de documentación y estilos Tailwind; no hay referencias desde vistas, rutas o controladores ni ruta activa que la sirva. | Plantilla huérfana de prototipado, fuera de la arquitectura visual vigente. | Eliminar la vista huérfana. |

Las vistas de autenticación, perfil, formularios, navegación móvil, selector de tema, acciones de correo y cierre de diálogos revisadas contienen acciones reales o son controles de accesibilidad; no se propone eliminarlas. La inspección tampoco encontró enlaces `href="#"` o manejadores `onclick` vacíos en las vistas Blade activas.

## Capabilities

### New Capabilities

Ninguna.

### Modified Capabilities

- `accessible-crm-interactions`: exigir que los controles visibles indiquen una acción/destino real y que una misma acción de estado no se presente duplicada; retirar controles decorativos o engañosos sin degradar las alternativas accesibles.

## Impact

- Vistas Blade del layout y de dashboard, actividades, contactos, empresas y oportunidades; retiro de `resources/views/welcome.blade.php`.
- Revisión acotada de CSS/JS para limpiar solo reglas y código huérfanos después de la aprobación de la auditoría.
- No se proponen cambios de dependencias, rutas de API, modelos, ni reemplazo de componentes o frameworks.
