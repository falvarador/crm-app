# Aura CRM

Plataforma ligera, rápida y moderna para la gestión de relaciones con clientes (CRM), construida sobre **Laravel** con una **arquitectura híbrida (SSR + Islas de Reactividad)** que elimina la sobrecarga de los frameworks SPA tradicionales.

---

## Características Principales

- **Gestión Integral de Clientes:** Control completo de cuentas comerciales (empresas), contactos, prospectos y conversión rápida a clientes activos.
- **Pipeline Visual de Ventas:** Gestión de oportunidades por etapas con vista de lista y tablero Kanban interactivo.
- **Seguimiento y Actividades:** Registro cronológico polimórfico de llamadas, correos, reuniones, tareas y notas vinculadas a cualquier entidad.
- **Islas de Reactividad (Zero-Framework JS):** Modales, notificaciones toast, búsqueda en tiempo real y actualizaciones parciales del DOM sin recargar la página mediante **HTML Web Components (Light DOM)** nativos.
- **Validación Sincronizada Cliente/Servidor:** Protección no intrusiva basada en la *Constraint Validation API* del navegador sincronizada con los `FormRequests` de Laravel.
- **Diseño Moderno y Responsivo:** CSS 100% nativo sin preprocesadores ni frameworks de utilidades, con soporte automático para modo claro y oscuro.

---

## Stack Tecnológico y Arquitectura

### Backend (Laravel Modular)
- **Framework:** Laravel (PHP 8.3+)
- **Base de Datos:** PostgreSQL / MySQL
- **Arquitectura:** Organización modular por dominios (`app/Modules/`) con controladores híbridos delgados, `FormRequests`, `Actions` y `Policies`.
- **Renderizado Parcial:** Motor **Blade** con componentes reutilizables (`<x-ui.*>`) y directivas `@fragment` para responder fragmentos HTML en peticiones asíncronas.

### Frontend (Estándares Web Nativos)
- **Light DOM Web Components (Vanilla ES Modules):**
  - `<crm-form-validate>`: Validación progresiva en `blur` e `input` con soporte para `[validate-msg]` y renderizado automático de errores `422` de Laravel en `[data-error-for]`.
  - `<crm-form-ajax>`: Envío asíncrono de formularios mediante `fetch` con gestión de estado `aria-busy` y emisión de eventos personalizados (`event-keys`).
  - `<crm-html-ajax>`: Escucha eventos del DOM y actualiza fragmentos HTML específicos (`X-Fragment-Request: true`) sin recargar la página.
  - `<crm-dialog>` y `<crm-toast>`: Gestión accesible de `<dialog>` nativos y alertas flotantes.
  - `<crm-kanban>`: Tablero Drag & Drop nativo sincronizado vía peticiones `PATCH`.
- **CSS Nativo Moderno:**
  - **Cascade Layers (`@layer`):** `reset`, `tokens`, `base`, `layout`, `components`, `utilities`.
  - **Sistema de Color Perceptual:** Paleta definida en `oklch()` con manipulación relativa (`oklch(from ...)`) y soporte nativo claro/oscuro mediante `light-dark()`.
  - **Selectores y Layouts Modernos:** CSS Nesting nativo (`&`), `:has()`, `:user-invalid`, Container Queries (`@container`), Subgrid, unidades lógicas y `@starting-style`.

---

## Estructura del Proyecto

```text
├── .agent/
│   └── rules/                  # Reglas de arquitectura para Antigravity
├── _design_refs/
│   └── stitch/                 # Referencias HTML/CSS exportadas desde Google Stitch
├── app/
│   └── Modules/
│       ├── Core/               # Autenticación, roles, layout base y UI compartida
│       ├── Companies/          # Cuentas comerciales / Empresas
│       ├── Contacts/           # Contactos y prospectos
│       ├── Deals/              # Pipeline de ventas y Kanban
│       ├── Activities/         # Tareas, notas y timeline polimórfico
│       └── Dashboard/          # KPIs ejecutivos y resumen diario
├── resources/
│   ├── css/
│   │   ├── app.css             # Punto de entrada y definición de @layer
│   │   ├── tokens.css          # Design tokens (oklch, light-dark, tipografía, espaciado)
│   │   ├── layout.css          # Estructura principal, sidebar y grids responsivos
│   │   └── components.css      # Estilos de componentes con CSS Nesting y :has()
│   ├── js/
│   │   ├── app.js              # Registro de Web Components
│   │   └── components/         # Light DOM Web Components (crm-*.js)
│   └── views/
│       └── components/ui/      # Componentes Blade semánticos (<x-ui.*>)