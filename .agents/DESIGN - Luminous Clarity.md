---
name: Luminous Clarity
colors:
  surface: '#f9f9fc'
  surface-dim: '#dadadc'
  surface-bright: '#f9f9fc'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f3f6'
  surface-container: '#eeeef0'
  surface-container-high: '#e8e8ea'
  surface-container-highest: '#e2e2e5'
  on-surface: '#1a1c1e'
  on-surface-variant: '#424656'
  inverse-surface: '#2f3133'
  inverse-on-surface: '#f0f0f3'
  outline: '#727687'
  outline-variant: '#c2c6d8'
  surface-tint: '#0054d6'
  primary: '#0050cb'
  on-primary: '#ffffff'
  primary-container: '#0066ff'
  on-primary-container: '#f8f7ff'
  inverse-primary: '#b3c5ff'
  secondary: '#585f66'
  on-secondary: '#ffffff'
  secondary-container: '#dce3eb'
  on-secondary-container: '#5e656c'
  tertiary: '#a33200'
  on-tertiary: '#ffffff'
  tertiary-container: '#cc4204'
  on-tertiary-container: '#fff6f4'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae1ff'
  primary-fixed-dim: '#b3c5ff'
  on-primary-fixed: '#001849'
  on-primary-fixed-variant: '#003fa4'
  secondary-fixed: '#dce3eb'
  secondary-fixed-dim: '#c0c7cf'
  on-secondary-fixed: '#151c22'
  on-secondary-fixed-variant: '#40484e'
  tertiary-fixed: '#ffdbd0'
  tertiary-fixed-dim: '#ffb59d'
  on-tertiary-fixed: '#390c00'
  on-tertiary-fixed-variant: '#832600'
  background: '#f9f9fc'
  on-background: '#1a1c1e'
  surface-variant: '#e2e2e5'
typography:
  headline-lg:
    fontFamily: Hanken Grotesk
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Hanken Grotesk
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
  button-text:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base-unit: 8px
  container-padding-desktop: 40px
  container-padding-mobile: 24px
  stack-gap: 24px
  input-gap: 8px
---

## Brand & Style

The design system is built on a foundation of **Corporate Minimalism**, designed to instill a sense of immediate trust, security, and effortless efficiency. The target audience consists of professionals and enterprise users who value clarity and speed over decorative flourish.

The emotional response is one of "calm confidence." By utilizing high-quality whitespace and a restricted color palette, the UI feels breathable and high-end. The style avoids the heaviness of traditional enterprise software, opting instead for a lightweight, modern aesthetic characterized by subtle depth and precise alignment.

## Colors

The palette is centered around a vibrant **Action Blue** (`#0066FF`), used strategically for primary interactions to draw the eye without overwhelming the visual field. 

- **Primary:** Use for main call-to-action buttons, active states, and focus indicators.
- **Secondary:** A soft tint of the primary blue, used for background fills on secondary buttons or subtle highlighting.
- **Neutral:** A deep "Midnight Blue" charcoal used for typography to ensure high legibility while appearing softer and more modern than pure black.
- **Surface:** The background remains a pristine white, with light gray borders (`#E2E8F0`) used for structural separation.

## Typography

This design system utilizes a tiered typography strategy to balance character with utility. 

**Hanken Grotesk** is the choice for headlines, offering a sharp, contemporary geometric feel that communicates modern tech-forwardness. **Inter** is utilized for all functional text, inputs, and body copy due to its exceptional legibility and systematic, neutral appearance. 

Maintain generous line heights (1.5x) for body copy to enhance readability and contribute to the "clean" aesthetic. Headlines should utilize tighter letter spacing to maintain a cohesive, "locked-in" visual weight.

## Layout & Spacing

The layout philosophy follows a **Fixed-Width Content** model for the login container (typically 440px) centered within a fluid viewport. This ensures the authentication experience remains focused and doesn't become visually overwhelming on ultra-wide displays.

The spacing rhythm is strictly based on an **8px grid system**. 
- **Vertical Rhythm:** Use 24px (`3x`) between major groups (e.g., between the header and the form) and 8px (`1x`) between labels and their respective input fields.
- **Safe Areas:** On mobile, ensure a minimum margin of 24px from the screen edge.
- **Alignment:** All form elements must be left-aligned to provide a single, strong vertical axis for the eye to follow.

## Elevation & Depth

To maintain a minimalist look, depth is communicated through **Ambient Shadows** rather than heavy borders or dark gradients.

- **The Login Card:** Should feature a very soft, diffused shadow (0px 10px 25px rgba(0, 0, 0, 0.04)). This lifts the card slightly off the white background without creating a harsh visual break.
- **Interactive Elements:** Buttons should have a subtle "lift" shadow only on hover to provide tactile feedback.
- **Focus States:** Use a 2px solid primary color ring with a 4px soft outer glow (offset 0) to indicate active input fields, replacing the standard browser outline.

## Shapes

The design system employs a **Rounded** aesthetic to soften the professional tone and make the UI feel approachable.

- **Primary Inputs & Buttons:** Use a radius of 8px (`0.5rem`). This strikes the perfect balance between the rigidity of sharp corners and the playfulness of full pills.
- **Container Cards:** Use 16px (`1rem`) for larger layout containers to emphasize their structural role.
- **Checkboxes:** Utilize a slightly smaller radius (4px) to maintain their distinct geometric identity while remaining cohesive with the broader system.

## Components

### Buttons
- **Primary:** Solid Primary Blue fill, White text. No border. Soft shadow on hover.
- **Secondary/Ghost:** Transparent background with a subtle border or light blue tint. Text in Primary Blue.

### Input Fields
- Background: Pure White or very light gray (`#F9FAFB`).
- Border: 1px solid `#E2E8F0`.
- Text: Neutral Black for input, medium gray for placeholders.
- Focus: Border color transitions to Primary Blue with a soft outer glow.

### Cards
- White background, 16px corner radius, and a thin subtle border (`1px solid #F1F5F9`) paired with an ambient shadow.

### Checkboxes
- 20px x 20px size. When checked, the background is Primary Blue with a white checkmark icon.

### Feedback/Status
- Success: Emerald Green (`#10B981`).
- Error: Crimson Red (`#EF4444`). Error messages should appear 4px directly below the input field in 12px font size.