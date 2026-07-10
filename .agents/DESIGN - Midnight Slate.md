---
name: Midnight Slate
colors:
  surface: '#0b1326'
  surface-dim: '#0b1326'
  surface-bright: '#31394d'
  surface-container-lowest: '#060e20'
  surface-container-low: '#131b2e'
  surface-container: '#171f33'
  surface-container-high: '#222a3d'
  surface-container-highest: '#2d3449'
  on-surface: '#dae2fd'
  on-surface-variant: '#c7c4d7'
  inverse-surface: '#dae2fd'
  inverse-on-surface: '#283044'
  outline: '#908fa0'
  outline-variant: '#464554'
  surface-tint: '#c0c1ff'
  primary: '#c0c1ff'
  on-primary: '#1000a9'
  primary-container: '#8083ff'
  on-primary-container: '#0d0096'
  inverse-primary: '#494bd6'
  secondary: '#b9c8de'
  on-secondary: '#233143'
  secondary-container: '#39485a'
  on-secondary-container: '#a7b6cc'
  tertiary: '#4edea3'
  on-tertiary: '#003824'
  tertiary-container: '#00885d'
  on-tertiary-container: '#000703'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#e1e0ff'
  primary-fixed-dim: '#c0c1ff'
  on-primary-fixed: '#07006c'
  on-primary-fixed-variant: '#2f2ebe'
  secondary-fixed: '#d4e4fa'
  secondary-fixed-dim: '#b9c8de'
  on-secondary-fixed: '#0d1c2d'
  on-secondary-fixed-variant: '#39485a'
  tertiary-fixed: '#6ffbbe'
  tertiary-fixed-dim: '#4edea3'
  on-tertiary-fixed: '#002113'
  on-tertiary-fixed-variant: '#005236'
  background: '#0b1326'
  on-background: '#dae2fd'
  surface-variant: '#2d3449'
typography:
  display-lg:
    fontFamily: Hanken Grotesk
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Hanken Grotesk
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Hanken Grotesk
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 36px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-sm:
    fontFamily: Geist
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 12px
  md: 24px
  lg: 40px
  xl: 64px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 48px
---

## Brand & Style
The design system is engineered for high-performance SaaS and developer-centric environments. It prioritizes clarity, focus, and a sophisticated aesthetic that reduces eye strain through a deep, multi-layered dark mode. 

The style is **Modern Corporate** with a hint of **Minimalism**. It uses wide letter spacing, generous whitespace even in dark containers, and a restricted but vibrant accent palette. The goal is to evoke a sense of precision and "quiet power"—where the interface recedes to let the user's data take center stage.

## Colors
This design system utilizes a "layered ink" philosophy for its dark theme. Instead of pure black (#000000), which causes high-contrast "smearing" on OLED screens, we use a deep Navy-Slate base (#020617). 

- **Primary:** An electric Indigo used for key actions and brand moments.
- **Surface Strategy:** Layers are defined by increasing lightness. The further an element "rises" towards the user, the lighter its slate-gray background becomes.
- **Accessibility:** All text-on-background combinations are strictly monitored to exceed WCAG AA standards. Neutral grays are tinted with 2% blue to maintain a "cool" professional temperature.

## Typography
The typographic hierarchy relies on the sharp, contemporary geometry of **Hanken Grotesk** for high-level headings. This provides a distinctive "tech-forward" personality. 

For functional data and long-form reading, **Inter** is used for its superior legibility in dark environments. **Geist** is reserved for labels, monospaced data, and small technical metadata to provide a developer-friendly feel. Headlines should use tighter tracking, while labels benefit from expanded tracking to ensure they don't "clump" in high-contrast settings.

## Layout & Spacing
The system follows a strict **8px linear scale**. Layouts are built on a **12-column fluid grid** for desktop and a **4-column grid** for mobile.

- **Desktop:** 48px outer margins with 24px gutters. Content is centered with a max-width of 1440px.
- **Mobile:** 16px outer margins. Elements should stack vertically, utilizing the full width of the container.
- **Spacing Logic:** Use `md` (24px) for internal card padding and `lg` (40px) for section vertical spacing to maintain an airy, premium feel despite the dark palette.

## Elevation & Depth
In this design system, depth is communicated through **Tonal Layering** and **Subtle Inner Glows** rather than heavy drop shadows.

1.  **Level 0 (Background):** #020617 (Deepest point).
2.  **Level 1 (Default Surface):** #0F172A (Navigation bars, cards).
3.  **Level 2 (Overlay/Modals):** #1E293B with a 1px border of #334155.
4.  **Interactive States:** When an element is hovered, its "glow" increases via a subtle 1px white border at 10% opacity, simulating a light source catching the edge of a physical object.

Avoid traditional black shadows, as they are invisible on the deep navy background. Use high-opacity borders to define boundaries instead.

## Shapes
The shape language is **Rounded**, providing a friendly counterpoint to the technical typography. 

- **Standard Components:** Buttons and input fields use a 0.5rem (8px) radius.
- **Containers:** Large cards and modals use 1rem (16px) to clearly distinguish them from the background grid.
- **Interactive Elements:** Active pills and tags use a fully rounded "pill" shape to denote their clickability and distinct status.

## Components
- **Buttons:** Primary buttons use a solid Indigo (#6366F1) with white text. Secondary buttons use a ghost style with a subtle slate border.
- **Input Fields:** Backgrounds should be one shade darker than the surface they sit on to create an "inset" feel. Use a 2px Indigo border for the focus state.
- **Chips/Tags:** Use low-saturation backgrounds (e.g., Indigo at 15% opacity) with high-saturation text for maximum readability without visual noise.
- **Cards:** Cards do not use shadows. They are defined by a 1px border (#334155). On hover, the border color shifts to the Primary Indigo or a lighter Slate.
- **Lists:** Use subtle horizontal dividers (#334155) with 12px vertical padding for list items to ensure touch targets are sufficient on mobile.