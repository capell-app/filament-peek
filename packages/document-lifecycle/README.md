# Document Lifecycle

Controlled document registration, publication history, and acceptance tracking for Capell.

## At A Glance

- Package: `capell-app/document-lifecycle`
- Namespace: `Capell\DocumentLifecycle\`
- Surfaces: Filament admin, database
- Service providers: `packages/document-lifecycle/src/Providers/DocumentLifecycleServiceProvider.php`
- Capell dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/publishing-studio`

## What It Adds

- A Controlled documents admin resource.
- Document registration and publication actions.
- Publication records linked to Publishing Studio revisions.
- Acceptance records stored in or extending the `legal_acceptances` table.
- Protected table registration for document and acceptance audit data.

Use this package when a site needs evidence that a controlled document was published and accepted. It is not a general file manager; media and downloadable assets stay in the media packages.

## Admin Surface

- Resource: `DocumentResource`.
- Pages: `ListDocuments`, `EditDocument`.
- Relation managers: `PublicationsRelationManager`, `AcceptancesRelationManager`.

## Data And Persistence

- Models: `Document`, `DocumentPublication`, `DocumentAcceptance`.
- Migrations: `document_lifecycle_documents`, `document_lifecycle_publications`, and `legal_acceptances` extension.
- Actions: register, publish, resolve latest publication, compute content hash, record acceptance.

## Install And Setup

Install with:

```bash
composer require capell-app/document-lifecycle
```

Run migrations through the host application package install flow.

## Docs

- [overview.md](docs/overview.md)

## Testing

Run package tests from the repository root:

```bash
vendor/bin/pest packages/document-lifecycle/tests --configuration=phpunit.xml
```
