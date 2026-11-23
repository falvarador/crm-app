# Tasks

## 1. Layout and dashboard

- [x] 1.1 Apply the approved layout inventory: make workspace branding non-interactive, remove the static demo domain, duplicate bell/help controls and redundant palette close action, and correct the sidebar section label; verify the remaining navigation, keyboard shortcuts, responsive sidebar, and dialog close controls still render and operate.
- [x] 1.2 Apply the approved dashboard inventory: remove unsupported live claims, correct the pipeline icon, consolidate task state changes into one accessible text-labeled toggle, and remove repeated counts, arrow decoration, and list initials; update `tests/Feature/CrmUiTest.php` to verify the rendered state and preserved task actions.

## 2. Activities, contacts, and companies

- [x] 2.1 Remove the activities eyebrow and duplicated summary card, then rebalance the form layout without an empty grid column; update `tests/Feature/CrmUiTest.php` to verify the page retains its activity form and primary pending/overdue/completed counts.
- [x] 2.2 Apply the approved contacts inventory: conditionally show the reset action, remove the generic opportunity shortcut, repeated initials/count and technical SSR note; update `tests/Feature/CrmUiTest.php` to verify reset remains available with filters and the table retains useful result counts.
- [x] 2.3 Apply the approved companies inventory: remove redundant filter/total/initial/icon decoration, show reset only when filters are active, and remove the generic opportunity shortcut; update `tests/Feature/CrmUiTest.php` to verify filtered empty states still offer a reset and company details remain navigable.

## 3. Opportunities and orphan view

- [x] 3.1 Pass each Kanban column's stage into the existing opportunity dialog and initialize its stage field before display; update `tests/Feature/CrmUiTest.php` to verify each stage trigger carries its correct context and retain the existing create form behavior.
- [x] 3.2 Remove the unreferenced Laravel welcome view after confirming no route or source reference uses it; verify `/` still renders the CRM dashboard and no `welcome` view reference remains.
- [x] 3.3 Remove only CSS selectors and JavaScript handlers proven unused after the approved markup changes; verify each removed selector/handler has no references in Blade, dynamic class construction, or active component behavior.

## 4. Integration verification

- [x] 4.1 Run `php artisan test --compact tests/Feature/CrmUiTest.php` and `node --test tests/crm-html-ajax.test.js`; verify all updated rendered-page expectations and existing async component behavior pass.
- [x] 4.2 Run `npm run build` and inspect the final diff; verify there are no layout regressions, unintended component-contract changes, new dependencies, or unapproved inventory edits.
