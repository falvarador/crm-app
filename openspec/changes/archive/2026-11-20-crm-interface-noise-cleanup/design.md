# Design

## Context

See `proposal.md` for the audit inventory and motivation. The affected screens use Blade views, shared UI components, native CSS, and event-delegated JavaScript. Dialogs open through the existing `data-dialog-open` contract, and activity/deal changes use the existing form and AJAX components.

## Goals / Non-Goals

**Goals:**

- Apply only the inventory items approved by the user.
- Preserve the existing routes, forms, keyboard operation, accessible names, and component contracts.
- Remove CSS/JavaScript only after confirming it has no remaining static or dynamically generated use.

**Non-Goals:**

- Add frameworks, dependencies, new CRM workflows, or backend associations solely to preserve generic shortcut buttons.
- Redesign unrelated views, theme tokens, or the behavior of shared Web Components.

## Decisions

1. **Keep the inventory as the implementation boundary.** Each Blade change must correspond to an approved row in `proposal.md`. Existing dialog close buttons, mobile navigation controls, theme controls, form actions, and meaningful navigation remain intact; a repeated icon is not by itself evidence that a control is dead.

2. **Preserve column context for Kanban creation.** Each stage action should pass its stage to the existing create dialog and initialize the existing stage field before the dialog is shown. Use a stage data attribute and narrowly scoped delegated event handling rather than changing the `<crm-dialog>` contract. Removing the per-column actions is the fallback only if this context cannot be preserved cleanly.

3. **Keep contextual navigation honest.** Do not add company/contact-to-deal relationships or new filters just to support the current generic shortcuts. Remove those shortcuts unless the approved scope includes an existing contextual destination; the main Opportunities navigation remains available.

4. **Rebalance layout when duplicated content is removed.** Remove empty wrappers/columns left by the activities summary card, and use existing layout classes so the activity form does not leave a blank grid column or become excessively wide. Avoid introducing new design tokens or layout abstractions for isolated edits.

5. **Clean up only proven orphans.** After the approved Blade edits, search all templates, component slots, scripts, and dynamically assembled class names before deleting CSS rules or JavaScript handlers. Preserve selectors and handlers used by active components even if their visible controls change.

## Risks / Trade-offs

- Removing generic shortcuts from a contact/company row reduces one-click navigation to the pipeline → the existing Opportunities navigation remains available, and no existing route provides the promised contextual result.
- Consolidating task controls could accidentally remove reopening → use one text-labeled toggle whose label and accessible name reflect the next action in both states.
- Removing repeated metrics or avatars could reduce quick scanning → retain the primary count and meaningful labels, and remove only the duplicate presentation documented in the inventory.
- Removing the welcome view could affect an untracked external integration → repository routes and source references do not use it; verify route and view references again before deletion.
- Removing markup may leave layout gaps or unused styles → inspect responsive layouts and all selector references after implementation; do not infer orphan status from a single view.

## Migration Plan

No data migration or route migration is planned. Once the inventory is approved, implement the Blade changes, then perform the scoped CSS/JS orphan check and run the narrowest relevant tests/build checks. Roll back by reverting only the approved view/style/script edits if a retained flow or layout regresses.
