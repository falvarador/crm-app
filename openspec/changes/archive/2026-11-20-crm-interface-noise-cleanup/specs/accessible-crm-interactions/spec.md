# Spec Delta

## ADDED Requirements

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
