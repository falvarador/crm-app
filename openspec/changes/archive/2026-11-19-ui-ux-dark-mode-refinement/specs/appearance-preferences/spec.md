# Spec Delta

## Purpose

Permitir una apariencia coherente y legible que respete las preferencias visuales del usuario y del sistema operativo.

## ADDED Requirements

### Requirement: Preferencia de apariencia
El sistema SHALL ofrecer las preferencias `system`, `light` y `dark` y SHALL usar `system` cuando no exista una preferencia válida guardada.

#### Scenario: Preferencia predeterminada del sistema
- **WHEN** una persona abre el CRM sin una preferencia válida guardada
- **THEN** la apariencia refleja el tema actual del sistema operativo y el selector indica `system`

#### Scenario: Cambio del sistema operativo
- **WHEN** cambia el tema del sistema operativo mientras está seleccionada la preferencia `system`
- **THEN** la interfaz refleja el nuevo tema sin recargar la página

#### Scenario: Selección manual
- **WHEN** una persona selecciona `light` o `dark`
- **THEN** el CRM aplica y guarda esa preferencia, y posteriores cambios del sistema operativo no la sustituyen

#### Scenario: Volver a la preferencia del sistema
- **WHEN** una persona selecciona `system` después de utilizar un tema manual
- **THEN** el CRM vuelve a seguir el tema actual del sistema y conserva esa elección en navegaciones posteriores

### Requirement: Apariencia antes del primer pintado
El sistema SHALL restaurar una preferencia válida antes del primer pintado de los layouts autenticado y de invitado, evitando un destello del tema contrario durante la navegación SSR.

#### Scenario: Navegación con preferencia manual guardada
- **WHEN** se abre o recarga una página con `light` o `dark` guardado y distinto del tema del sistema
- **THEN** el primer pintado usa la preferencia guardada sin mostrar previamente el tema contrario

#### Scenario: Almacenamiento inaccesible
- **WHEN** el almacenamiento de preferencias no está disponible
- **THEN** la página sigue siendo utilizable con la apariencia del sistema y las demás interacciones continúan funcionando

### Requirement: Contraste y controles nativos
El sistema SHALL garantizar una relación de contraste de al menos 4.5:1 para texto normal y 3:1 para límites e indicadores visuales necesarios de controles y estados, en ambos temas y sus estados interactivos. Las superficies oscuras SHALL evitar negro puro y mantener niveles de elevación distinguibles.

#### Scenario: Texto y controles en ambos temas
- **WHEN** se inspeccionan texto, campos, botones y sus estados visibles en los temas claro y oscuro
- **THEN** sus combinaciones de colores computadas cumplen los umbrales de contraste aplicables

#### Scenario: Controles nativos y superficies elevadas
- **WHEN** se visualizan scrollbars, fechas, selectores y diálogos en modo oscuro
- **THEN** respetan el esquema oscuro y no presentan superficies claras ajenas al diseño
