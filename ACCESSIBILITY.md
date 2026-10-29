# Accessibility report (WCAG 2.2 level AA)

## Method

- Automated: axe-core 4.10.2 in headless Chrome, rules `wcag2a`, `wcag2aa`, `wcag21a`, `wcag21aa`, `wcag22aa` and best practices.
- Matrix: light and dark theme × 1280, 768 and 320 px × four states (empty form, result, validation errors, open modal).
- Result: **0 violations**. A negative control (an injected low-contrast paragraph and an image without `alt`) was detected, so the run is able to fail.
- Contrast ratios for the design tokens were computed separately in Phase 2 (all pairs pass).

## Fixes applied during the audit

| Criterion | Finding | Fix |
| --- | --- | --- |
| 1.4.11 Non-text contrast | Input borders were about 1.6:1 | Borders use `--md-sys-color-outline` (4.6:1 light, 5.9:1 dark) |
| 1.4.3 Contrast (minimum) | Bootstrap invalid-feedback red and outline-secondary gray fall below 4.5:1 | Colors mapped to theme tokens |
| 2.4.7 / 2.4.13 Focus visible | Bootstrap replaces outlines with a blue shadow on buttons, links and inputs | One 3 px outline restated for every component |
| 2.4.11 Focus not obscured | Sticky app bar could cover focused fields | `scroll-padding-top` on `html` |
| 2.5.8 Target size | Buttons and inputs were 31–38 px | Minimum 44 px for buttons, inputs, close button, icon buttons |
| 1.3.1 Info and relationships | Drawer title was a heading placed before the `h1` | Changed to a paragraph |
| 4.1.2 Name, role, value | Active section was only a CSS class | Scrollspy now sets `aria-current="location"` |
| 2.1.1 Keyboard | Scrollable table region was not focusable | `role="region"`, `aria-label`, `tabindex="0"` |
| 1.4.10 Reflow | Formula could scroll horizontally | Text wraps; no horizontal page scroll at 320 px |
| 2.3.3 / smooth scroll | Scrollspy smooth scroll ignored reduced motion | Smooth scroll removed; native anchors are used |
| Icon fonts | Ligature names flashed as text while loading | `font-display: block` for Material Symbols |

## Manual checks still recommended

- Screen reader pass (NVDA + Firefox/Chrome): result announcement in the `role="status"` region and the modal.
- 200% and 400% browser zoom on a real device.
- Keyboard-only walkthrough on a phone with an external keyboard.

## Known limitation

The sidebar highlight can lag one section behind on very short viewports (cosmetic; navigation and `aria-current` still work).
