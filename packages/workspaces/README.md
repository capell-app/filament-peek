# Capell Workspaces

Workspaces gives Capell editors a safe draft and approval layer. Editors can group related changes, preview them together, request review, schedule publishing, and roll back versions without pushing half-finished content live.

## When to install it

Install Workspaces when more than one person edits content, when campaigns need staged changes, or when publishing requires approval checks.

## Quick install

```bash
composer require capell-app/workspaces
php artisan migrate
php artisan optimize:clear
```

The package registers through Laravel discovery and adds admin, console, and shared providers.

## What appears in the admin

| Area               | What editors can do                                     |
| ------------------ | ------------------------------------------------------- |
| Workspace switcher | Move between live content and draft workspaces          |
| Page editor        | Save drafts, preview changes, and request review        |
| Approval panels    | Review decisions, comments, history, and publish checks |
| Preview links      | Share signed draft previews without publishing          |

![Workspace pages list](docs/images/screenshots/01-pages-list.png)

## What developers get

- Draftable model support through workspace-aware traits and contracts.
- Copy-on-write publishing, diffing, rebasing, rollback, and scheduled publish actions.
- Publish checks for accessibility, broken links, missing alt text, and SEO metadata.
- Events and notifications for workspace state changes.

## Deeper docs

- [Workspaces overview](docs/workspaces.md)
- [Page drafts and publishing](docs/page-drafts-and-publishing.md)
- [Page creation and approval flow](docs/page-creation-and-approval-flow.md)
- [Extending workspaces](docs/extending-workspaces.md)
- [Draftable contract](docs/workspaces-draftable-contract.md)
