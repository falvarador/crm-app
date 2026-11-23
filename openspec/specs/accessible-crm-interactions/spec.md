# accessible-crm-interactions Specification

## Purpose

Mantener los flujos comerciales del CRM utilizables y comprensibles con teclado, tecnología asistiva y pantallas de distintos tamaños.

## Requirements

### Requirement: Diálogos accesibles
Los diálogos y paneles de creación SHALL abrirse dentro del área visible, mantener el foco dentro mientras sean modales y devolverlo al elemento que los abrió al cerrarse. SHALL permitir el cierre con Escape y mediante el fondo cuando esté habilitado.

#### Scenario: Abrir y cerrar un panel de creación
- **WHEN** una persona abre un panel de creación y luego lo cierra con Escape
- **THEN** el panel es visible, el foco permanece en el diálogo mientras está abierto y vuelve al control de apertura al cerrarse

#### Scenario: Cerrar mediante el fondo
- **WHEN** una persona hace clic en el fondo de un diálogo configurado para permitirlo
- **THEN** el diálogo se cierra sin activar controles situados detrás

### Requirement: Navegación accesible y responsiva
La navegación móvil SHALL reflejar su estado abierto o cerrado y SHALL impedir que los elementos ocultos reciban foco. Los controles de pestañas SHALL exponer el estado seleccionado correcto y permitir el uso de teclado apropiado a su semántica.

#### Scenario: Menú móvil cerrado
- **WHEN** el menú móvil está cerrado
- **THEN** sus enlaces no reciben foco por tabulación y el control anuncia su estado cerrado

#### Scenario: Cambiar de pestaña
- **WHEN** una persona cambia una vista o filtro con teclado o puntero
- **THEN** el estado accesible coincide con la opción activa y el contenido correspondiente

### Requirement: Cargas y errores comprensibles
Las operaciones asíncronas SHALL ofrecer indicación visual y semántica de carga sin permitir envíos duplicados; la indicación SHALL permanecer activa hasta que termine la solicitud vigente. Los errores de validación SHALL asociarse a sus controles, anunciarse y permitir su corrección sin cambios bruscos de diseño.

#### Scenario: Envío en curso
- **WHEN** se envía un formulario asíncrono
- **THEN** se indica que está ocupado, se deshabilita su envío mientras está pendiente y se restablece al finalizar

#### Scenario: Actualizaciones consecutivas
- **WHEN** se inicia una actualización de fragmento antes de finalizar la anterior
- **THEN** el estado ocupado permanece hasta que termina la actualización más reciente y se muestra su resultado

#### Scenario: Error de validación
- **WHEN** una validación de cliente o servidor detecta un error
- **THEN** el campo se identifica como inválido, su mensaje asociado se anuncia y el primer campo inválido recibe foco al enviar

### Requirement: Tablas, Kanban y estados vacíos utilizables
Las tablas y tarjetas SHALL presentar importes con cifras alineadas, distinguir información principal y secundaria y mantener legible el contenido largo. Los estados vacíos SHALL explicar la situación y ofrecer una acción directa pertinente cuando exista una acción disponible.

#### Scenario: Datos extensos y pantallas estrechas
- **WHEN** una tabla o tarjeta contiene nombres largos o se visualiza en un contenedor estrecho
- **THEN** el contenido permanece accesible sin pérdida de funcionalidad y las cifras monetarias conservan su alineación

#### Scenario: Sin resultados o registros
- **WHEN** un listado o vista de tareas no contiene datos
- **THEN** muestra un mensaje adecuado y una acción directa pertinente cuando esté disponible

#### Scenario: Mover una oportunidad sin arrastrar
- **WHEN** una persona utiliza el selector de etapa de una tarjeta con teclado o dispositivo táctil
- **THEN** puede cambiar la etapa y recibe confirmación o información del error

### Requirement: Interacción adaptable
El sistema SHALL respetar `prefers-reduced-motion` y SHALL proporcionar objetivos táctiles adecuados sin impedir el uso de controles existentes en pantallas estrechas.

#### Scenario: Preferencia de movimiento reducido
- **WHEN** está activa la preferencia de movimiento reducido
- **THEN** las transiciones de desplazamiento y escala se reducen sin ocultar la retroalimentación de estado

#### Scenario: Controles en móvil
- **WHEN** se usan los controles principales en una pantalla táctil estrecha
- **THEN** sus áreas de interacción alcanzan al menos 44 por 44 píxeles CSS o disponen de una alternativa equivalente accesible

### Requirement: Controles con propósito y acciones no duplicadas
Los controles visibles SHALL ejecutar una acción o navegar al destino que su etiqueta y contexto prometen. La interfaz SHALL evitar presentar varios controles que ejecuten la misma transición sobre el mismo registro, salvo que proporcionen affordances claramente distintas. Al consolidar controles, SHALL conservar la acción equivalente accesible por teclado y tecnología asistiva.

#### Scenario: Etiqueta y destino coherentes
- **WHEN** una persona activa un control visible
- **THEN** el resultado corresponde con la acción, el destino y el contexto indicados por su etiqueta

#### Scenario: Una sola acción para cambiar el estado de un registro
- **WHEN** una fila o tarjeta ofrece más de un control para ejecutar la misma transición de estado
- **THEN** presenta un único control para esa transición y su nombre accesible describe la acción disponible

#### Scenario: Control consolidado accesible
- **WHEN** se elimina un control duplicado para una acción que debe seguir disponible
- **THEN** el control restante conserva el mismo resultado funcional y puede operarse y comprenderse con teclado y tecnología asistiva

### Requirement: Continuidad visual de navegación y contenido
El CRM SHALL proporcionar transiciones breves y discretas durante la navegación entre módulos y los cambios de vista iniciados por la persona usuaria cuando el navegador las admita. La navegación y los resultados SHALL seguir disponibles inmediatamente sin animación cuando el navegador no admita las transiciones o estas se omitan.

#### Scenario: Navegación entre módulos
- **WHEN** una persona navega entre páginas del CRM en un navegador compatible y sin preferencia de movimiento reducido
- **THEN** se presenta una transición visual breve hacia el nuevo contenido sin alterar el destino ni la navegación del navegador

#### Scenario: Cambio de vista asíncrono
- **WHEN** una persona cambia entre vistas o filtros que actualizan contenido del CRM
- **THEN** el contenido actualizado aparece con continuidad visual sin retrasar la interacción ni ocultar la indicación de carga

#### Scenario: Navegador sin soporte
- **WHEN** el navegador no admite transiciones de vista
- **THEN** la navegación y la actualización de contenido funcionan sin animación y conservan sus resultados y controles

#### Scenario: Actualizaciones interrumpidas o simultáneas
- **WHEN** una actualización es sustituida, falla o forma parte de un cambio de varios fragmentos
- **THEN** no se muestra contenido obsoleto ni parcialmente confirmado y los estados de carga y error siguen correspondiendo a la operación vigente

### Requirement: Movimiento funcional y accesible
El movimiento SHALL comunicar continuidad o retroalimentación sin bloquear el uso del CRM. Con movimiento reducido activado, SHALL omitir las transiciones de vista y el desplazamiento o escala no esenciales, manteniendo visible la retroalimentación y preservando foco, teclado y estados accesibles.

#### Scenario: Movimiento reducido
- **WHEN** la persona activa la preferencia del sistema para reducir movimiento
- **THEN** la navegación y los cambios de contenido ocurren sin transición de vista ni movimiento no esencial y los estados y resultados siguen visibles

#### Scenario: Uso con teclado y diálogos
- **WHEN** una persona navega o abre y cierra un diálogo con teclado
- **THEN** el foco y los controles permanecen disponibles y el diálogo conserva su comportamiento accesible independientemente de las animaciones
