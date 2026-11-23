# Spec Delta

## ADDED Requirements

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
