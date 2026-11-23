# Proposal

## Why

Los cambios entre módulos y vistas del CRM funcionan, pero pueden sentirse abruptos y dificultar la continuidad visual. Un lenguaje de movimiento breve y funcional puede hacer más comprensibles la navegación y los cambios de contenido sin ralentizar el trabajo repetitivo.

## What Changes

- Incorporar transiciones nativas entre páginas SSR compatibles y entre vistas o fragmentos relevantes del CRM, manteniendo la navegación normal como alternativa.
- Refinar la retroalimentación de controles, diálogos y cambios de estado existentes con movimiento discreto y coherente, evitando animaciones decorativas repetitivas.
- Respetar la preferencia de movimiento reducido y mantener intactos foco, teclado, estados accesibles y resultados de las operaciones.
- Garantizar que las actualizaciones asíncronas, simultáneas o interrumpidas sigan mostrando el resultado vigente sin depender de que una animación termine.

## Capabilities

### New Capabilities

Ninguna.

### Modified Capabilities

- `accessible-crm-interactions`: continuidad visual en navegación y actualizaciones, con degradación funcional y respeto a las preferencias de movimiento.

## Impact

- CSS nativo en capas y tokens de movimiento, layout Blade compartido y JavaScript de navegación y fragmentos.
- Posibles ajustes acotados de transiciones de diálogos, toasts y controles existentes, sin cambiar sus contratos ni los flujos comerciales.
- Pruebas JavaScript y verificación en navegador de navegación SSR, fragmentos, interacción y accesibilidad; sin dependencias ni frameworks nuevos.
