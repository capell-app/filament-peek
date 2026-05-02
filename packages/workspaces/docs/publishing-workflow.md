# Workspaces Publishing Workflow

This focused guide extends [Overview](overview.md) for the Workspaces package.

## Purpose

Workspaces controls the draft, approval, schedule, publish, rollback, and preview lifecycle for Draftable Capell records.

## Workflow

1. Create a draft workspace or page draft.
2. Edit draftable records inside workspace context.
3. Request review and collect approval decisions.
4. Run publish checks.
5. Publish immediately, schedule publishing, or request changes.
6. Use version history and rollback when a published version needs to be restored.

## Gates

- `submit_workspace`
- `approve_workspace`
- `publish_workspace`
- `rollback_workspace`
- `publish_outside_release_window`

## Draftable Contract

- Draft/publish models must implement the Capell Draftable contract.
- Models must be registered for workspace copy-on-write behaviour.
- Package integrations should use Workspaces actions instead of writing live rows directly.

## Screenshot Requirements

- Workspace index.
- Compare version page.
- Preview links manager.
- Scheduled publishing page.
- Stale drafts page.
- Frontend preview banner.
