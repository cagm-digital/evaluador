# Evaluador

Single-page web app for students of Instituto Técnico Superior de Pereira. The student enters grades for periods 1, 2 and 3 (20% each) and the app returns the minimum grade needed in period 4 (40%) to pass with 3.0.

Status: all planned phases (see `PROMPT_FASES.md`) are implemented, verified and committed on `main`. The user pushes; do not run `git push`.

## Stack and constraints

- PHP vanilla, kept compatible with PHP 7.4 (no PHP 8-only syntax or types). Tested on 8.0.30 and 8.5.0.
- Bootstrap 5.3.8 from jsdelivr with SRI, Material Symbols, Roboto. No build step, no Composer.
- Target: shared hosting with cPanel/Apache. `index.php` lives in the project root.
- Design: Material Design 3 tokens, light and dark themes, glassmorphism, moderate Bento layout, youthful look (vivid green + mint accent). Palette stays compatible with the school crest (greens, white, gray, black).
- WCAG AA is a hard requirement (contrast, 44px targets, visible focus, reduced motion, live regions).

## Layout

- `index.php`: page, no-JS POST fallback, formula modal.
- `calculate.php`: JSON endpoint (POST only; 422 on validation or CSRF errors).
- `includes/config.php`: institution data, `PASSING_GRADE=3.0`, `MIN_GRADE=1.0`, `MAX_GRADE=5.0`, `PERIOD_WEIGHTS`.
- `includes/functions.php`: escaping, asset versioning, CSP nonce, secure session, CSRF, `parseGrade()`, `calculateMinimumGrade()`, partial rendering.
- `includes/result.php`, `includes/breakdown.php`: result summary (inside the live region) and breakdown table (outside it).
- `assets/css/theme.css`: design tokens and Bootstrap bridge. `assets/css/styles.css`: layout and components.
- `assets/js/app.js`: scrollspy, drawer, theme toggle, calculator (validation, stepper, fetch, calculating animation).
- `tests/calculation_test.php`: CLI tests for the calculation logic.

## Business rules

- `N4 = (3.0 - 0.2 * (N1 + N2 + N3)) / 0.4`, scale 1.0 to 5.0.
- Status `approved` when the raw value is `<= MIN_GRADE`, `unreachable` when it is `> MAX_GRADE`, otherwise `reachable`.
- For `unreachable`, the UI shows the grade that would be required, the best possible final grade with 5.0 in period 4, and how many points are missing.
- Grade input accepts `.` or `,` as decimal separator, up to 2 decimals.

## Conventions

- Grade inputs are `type="text" inputmode="decimal"` with `role="spinbutton"`. `type="number"` drops `3,5` in some browsers and locales. The arrow buttons are custom, `aria-hidden`, and step by 0.1.
- The calculate action shows a ~900 ms animation (skipped with `prefers-reduced-motion`). Anything that waits for a result must wait for it to finish.
- The app bar height is the CSS variable `--app-bar-h` (it follows the crest size). Use it instead of hard-coded pixel offsets.
- Colors come from tokens in `theme.css`; do not hard-code colors in components. Every new color pair must keep AA contrast in both themes.
- Security: CSRF token in the session, hardened session cookie, CSP with a per-request nonce, security headers. Do not add inline scripts without the nonce.
- `.htaccess` blocks `includes/`, `tests/`, `.atl`, `.git` and `.md` files. `APP_DEBUG` stays `false`.
- Conventional Commits, no AI attribution lines. Stage with `git add -A -- . ':!.atl'`; `.atl/` is tooling cache and stays untracked.
- Generated technical artifacts (code, comments, UI copy, docs, commits) are in English. UI text shown to students is Spanish.

## Verification

- Logic: `php tests/calculation_test.php` (29 checks) on PHP 8.0 and 8.5. PHP binaries are under `C:\wamp64\bin\php\`.
- App runs at `http://localhost/evaluador/` through WAMP.
- Browser and accessibility checks (Playwright on Chrome, Edge, Firefox, WebKit; axe-core 4.10.2) were run with scripts kept outside the repo. The last full run had 0 failures and 0 axe violations in light/dark at 1280, 768 and 320 px, including the empty, result, unreachable, error and modal states.

## Known limitations

- Not tested on real iPhone or Android devices, nor with NVDA.
- `mod_deflate` and `mod_expires` rules are untested locally (WAMP does not load them); verify on cPanel.
- Not tested on PHP 7.4 (lowest installed is 8.0.30).
- Sidebar highlight can lag one section on very short viewports (cosmetic).
- No Open Graph tags: a share-preview change was tried and reverted at the user's request. The page `<title>` is "Evaluador | Instituto Técnico Superior de Pereira".
