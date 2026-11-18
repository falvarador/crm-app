---
name: Aura CRM
colors:
  surface: '#faf8ff'
  surface-dim: '#d2d9f4'
  surface-bright: '#faf8ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f3ff'
  surface-container: '#eaedff'
  surface-container-high: '#e2e7ff'
  surface-container-highest: '#dae2fd'
  on-surface: '#131b2e'
  on-surface-variant: '#444654'
  inverse-surface: '#283044'
  inverse-on-surface: '#eef0ff'
  outline: '#747686'
  outline-variant: '#c4c5d6'
  surface-tint: '#3052d2'
  primary: '#1a40c2'
  on-primary: '#ffffff'
  primary-container: '#3b5bdb'
  on-primary-container: '#e2e5ff'
  inverse-primary: '#b8c3ff'
  secondary: '#505f76'
  on-secondary: '#ffffff'
  secondary-container: '#d0e1fb'
  on-secondary-container: '#54647a'
  tertiary: '#005b3d'
  on-tertiary: '#ffffff'
  tertiary-container: '#007650'
  on-tertiary-container: '#76ffc2'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dde1ff'
  primary-fixed-dim: '#b8c3ff'
  on-primary-fixed: '#001355'
  on-primary-fixed-variant: '#0736ba'
  secondary-fixed: '#d3e4fe'
  secondary-fixed-dim: '#b7c8e1'
  on-secondary-fixed: '#0b1c30'
  on-secondary-fixed-variant: '#38485d'
  tertiary-fixed: '#6ffbbe'
  tertiary-fixed-dim: '#4edea3'
  on-tertiary-fixed: '#002113'
  on-tertiary-fixed-variant: '#005236'
  background: '#faf8ff'
  on-background: '#131b2e'
  surface-variant: '#dae2fd'
typography:
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 26px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.015em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 28px
    letterSpacing: -0.015em
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
    letterSpacing: -0.01em
  title-md:
    fontFamily: Inter
    fontSize: 15px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: -0.005em
  body-lg:
    fontFamily: Inter
    fontSize: 15px
    fontWeight: '400'
    lineHeight: 22px
  body-md:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
  label-sm:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
    letterSpacing: 0.02em
  numeric-metric:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 24px
    letterSpacing: -0.02em
  numeric-table:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '500'
    lineHeight: 18px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-desktop: 1.5rem
  margin: 1rem
  margin-desktop: 2rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 0.75rem
  space-lg: 1rem
  space-xl: 1.5rem
---

## Brand & Style

This design system delivers a focused, hyper-clarified B2B CRM experience anchored in the aesthetic traditions of modern productivity tools like Linear, Attio, and Folk. The brand tone is composed, analytical, and effortless. Interfaces avoid visual friction, noisy gradients, and decorative ornamentation, prioritizing high signal-to-noise ratios, rapid visual triage, and structured spatial harmony.

The target audience comprises modern sales engineers, founders, and account executives who spend hours daily scanning customer graphs, moving pipelines, and tracking multi-touch deals. The emotional response must be an immediate sense of control, calm operational momentum, and crystalline legibility.

The design philosophy combines **Airy Minimalism** with **Precision Data Density**:
- **Canvas-First Structure:** Interfaces rely on an ultra-light cool canvas overlaid with crisp white operational cards.
- **Micro-Interaction Fidelity:** Subtle borders, fast 150ms transitions, and deliberate focus states that make the tool feel lightweight and responsive.
- **Editorial Data Presentation:** Native Spanish terminology rendered in crisp geometric typography with strict tabular alignment for figures, statuses, and dates.

## Colors

The color architecture is built around high functional legibility, using deep indigo as a sharp, purposeful accent against an airy neutral canvas.

### Core Architecture
- **Primary Canvas (`#F8FAFC`):** Off-white base background providing optical rest and high contrast for overlaid white surfaces.
- **Surface Elevation (`#FFFFFF`):** Pure white container surfaces for cards, detail panes, tables, and modal dialogs.
- **Border Default (`#E2E8F0`):** Single-pixel perimeter boundary for cards, input containers, and structural dividers.
- **Border Subtle (`#F1F5F9`):** Internal cell and list dividers.
- **Brand Action (`#3B5BDB`):** Primary interactions, focused selection highlights, active pipeline milestones, and key confirmations.
- **Text Primary (`#0F172A`):** Deal titles, metrics, table data, and primary headings.
- **Text Secondary (`#64748B`):** Supporting metadata, labels, table column headers, and secondary timestamps.
- **Text Subtle (`#94A3B8`):** Placeholder hints, disabled glyphs, and inactive indicators.

### Semantic Status Badges (Soft Pastels)
All status badges pair a muted pastel tint background with a rich, dark-toned foreground text to guarantee AAA readability:
- **Prospecto / Nuevo (Sky):** Background `#E0F2FE`, Foreground `#0369A1`
- **Activo / Ganado / Calificado (Emerald):** Background `#DCFCE7`, Foreground `#15803D`
- **Propuesta / Negociación (Amber):** Background `#FEF3C7`, Foreground `#B45309`
- **Perdido / Inactivo (Coral):** Background `#FEE2E2`, Foreground `#B91C1C`

## Typography

The typographic hierarchy enforces immediate legibility and optical composure across complex data sheets.

- **Headlines (`Plus Jakarta Sans`):** Clean, geometric architecture with subtle personality. Applied strictly to view titles, board headers, and major section banners.
- **Data, Content & Controls (`Inter`):** Neutral, hyper-legible neo-grotesque font designed for complex UI density.
- **Tabular Figures Execution:** Every instance of financial figures (e.g. `€ 120.450`), pipeline velocity percentages (`+18.4%`), dates (`12 oct 2025`), and counts must enforce OpenType `font-variant-numeric: tabular-nums` (or CSS `font-feature-settings: "tnum"`). This prevents jitter when values refresh and maintains vertical column alignment.
- **Casing & Hierarchy:** Micro-labels and table column headers use `label-sm` or `label-md` with regular sentence case or controlled uppercase when paired with tracking (e.g., `FASE`, `RESPONSABLE`, `VALOR`).

## Layout & Spacing

This design system uses a flexible grid and structured dock layout engineered for data-dense multi-column workflows.

### Structure & Panes
- **Global App Shell:** Left-hand persistent navigation dock (fixed at 240px on desktop, collapsible to 64px icon-rail, off-canvas drawer on mobile).
- **Workspace Canvas:** Fluid horizontal workspace accommodating dynamic Kanban columns (fixed width 280px–320px) or multi-column data grids.
- **Contextual Inspector Panel:** Right-hand side sheet (fixed 420px) for contact profiles, deal activity feeds, and notes, overlaying or pushing the main canvas depending on available viewport.

### Breakpoints & Adaptations
- **Mobile (< 768px):** Margin `1rem`. Stacked single-column layouts; Kanban collapses into a tabbed segment control (`Prospectos`, `En Negociación`, `Ganados`); inspector opens full-screen.
- **Tablet (768px – 1023px):** Margin `1.5rem`. Collapsed 64px navigation rail; two-to-three visible columns with horizontal scroll snapping.
- **Desktop (≥ 1024px):** Margin `2rem`. Full multi-column view with concurrent inspector pane.

## Elevation & Depth

Visual hierarchy does not rely on heavy drop shadows or dramatic layering. Depth is expressed via a clean pairing of **1px structural borders** and **whisper-light ambient diffusion**.

### Layer Hierarchy
1. **Level 0 (Canvas Base):** Background `#F8FAFC`, flat, 0px elevation.
2. **Level 1 (Card & Content Blocks):** Pure white background (`#FFFFFF`), bounded by a `1px solid #E2E8F0` border. Shadow: `0 1px 2px 0 rgba(15, 23, 42, 0.04)`.
3. **Level 2 (Hovered Cards & Interactive Blocks):** On mouseover, borders shift to `#CBD5E1`. Shadow: `0 4px 6px -1px rgba(15, 23, 42, 0.06), 0 2px 4px -2px rgba(15, 23, 42, 0.04)`.
4. **Level 3 (Dropdowns, Popovers & Context Menus):** Background `#FFFFFF`, border `1px solid #E2E8F0`, shadow: `0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.03)`.
5. **Level 4 (Modal Dialogs & Command Bar):** Centered floating surfaces over an `#0F172A` overlay at 25% opacity with `backdrop-filter: blur(4px)`. Shadow: `0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.04)`.

## Shapes

The geometric form language is refined and consistent, balancing utility with softness:
- **Base Components (`rounded-md` / 8px):** Standard buttons, text input fields, selection menus, chips, contextual popovers, and table cells.
- **Containers & Surfaces (`rounded-lg` / 12px):** Pipeline deal cards, content modules, inspector sheets, search modals, and widget wrappers.
- **Badges & Avatars (`rounded-full` / 9999px):** Status badges, metric indicators, and user avatar initials or portraits.

## Components

All user-facing copy uses clean, natural Spanish enterprise terminology.

### Botones (Buttons)
- **Primario:** Background `#3B5BDB`, text `#FFFFFF`, 8px corner radius, height 36px (compact) or 40px (standard). Hover: `#334FB8`. Active: scale 0.99. Focus: `0 0 0 2px #FFFFFF, 0 0 0 4px #3B5BDB`.
- **Secundario:** Background `#FFFFFF`, border `1px solid #E2E8F0`, text `#0F172A`. Hover: background `#F8FAFC`, border `#CBD5E1`.
- **Fantasma / Terciario (Ghost):** Background transparent, text `#64748B`. Hover: background `#F1F5F9`, text `#0F172A`.
- **Destructivo:** Background `#FEF2F2`, border `1px solid #FCA5A5`, text `#DC2626`. Hover: background `#FEE2E2`.

### Pastillas de Estado (Status Badges / Chips)
- Pill silhouette (`border-radius: 9999px`), inline-flex, 4px vertical padding, 10px horizontal padding, typography `label-sm`.
- A 6px circular dot sits before the text with an identical hue at full saturation.
  - **Prospecto:** Dot `#0284C7`, Fondo `#E0F2FE`, Texto `#0369A1` ("Nuevo Prospecto")
  - **Calificado / Ganado:** Dot `#16A34A`, Fondo `#DCFCE7`, Texto `#15803D` ("Trato Ganado")
  - **Propuesta:** Dot `#D97706`, Fondo `#FEF3C7`, Texto `#B45309` ("En Negociación")
  - **Perdido:** Dot `#DC2626`, Fondo `#FEE2E2`, Texto `#B91C1C` ("Cerrado / Perdido")

### Campos de Entrada (Input Fields)
- Height 36px, background `#FFFFFF`, border `1px solid #E2E8F0`, radius 8px, text `body-md` (`#0F172A`), placeholder `#94A3B8`.
- Focus state: border `#3B5BDB`, outline none, box-shadow `0 0 0 1px #3B5BDB`.
- Inline currency & metric inputs feature pinned prefix tokens (e.g. `EUR €`) set in `tabular-nums` and muted gray `#64748B`.

### Tarjetas de Negocio / Trato (Deal Cards)
- Background `#FFFFFF`, radius 12px, border `1px solid #E2E8F0`, padding 12px.
- Layout: Top bar with company name and deal value (bold tabular numbers); middle section with primary contact avatar and title; bottom section with tag badge and last interaction date (e.g., `Último contacto: hace 2h`).
- Hover state translates card -1px vertically with subtle elevation boost.

### Tablas de Datos (Data Tables)
- Sticky header background `#F8FAFC`, border bottom `1px solid #E2E8F0`, headers set to `label-sm` with text `#64748B`.
- Row height: 44px dense, 52px default. Hover background: `#F8FAFC`.
- All monetary, date, and telephone columns right-aligned with `tabular-nums`.

### Casillas de Verificación y Selectores Radiales (Checkboxes & Radios)
- 16px square (checkbox) or circle (radio), border `1px solid #CBD5E1`, background `#FFFFFF`.
- Checked state: background `#3B5BDB`, border-color `#3B5BDB`, icon `#FFFFFF`.

### Barra de Comandos (Command Palette / Atajo Rápido)
- Modal centered at top 15% of viewport, max-width 640px, radius 12px, backdrop blurred.
- Quick navigation search across contactos, empresas, tratos, and recordatorios with keyboard hints (`⌘K`, `ESC`, `↵`).