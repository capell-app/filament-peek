# Capell Public Actions

Reusable public submit actions for Capell.

Public Actions lets a page, button, form submission, Zapier integration, or API client submit data to a named action. The package stores the submission, runs a registered handler, and can dispatch the handled payload to outbound destinations such as Zapier Catch Hook, Make, Pipedream, n8n, or a generic webhook.

## Concepts

- **Public action**: a named submit target, for example `access-gate.request` or `demo.request`.
- **Handler key**: a registered server-side handler. Stored actions reference keys, not PHP class names.
- **Destination**: an optional outbound adapter such as `http_webhook`.
- **Submission**: encrypted submitted payload plus safe metadata.
- **Integration token**: API/Zapier token with scoped abilities.

## Access Gate Buttons

Create a public action with handler key `access-gate.request`, then render:

```blade
<x-capell-access-gate::request-cta
    area="preview"
    public-action-key="preview-access"
    requested-url="{{ url()->current() }}"
/>
```

If `public-action-key` is omitted, the component posts to the built-in Access Gate request endpoint.

## Section Action Buttons

Content section action repeaters can use the `Public action` type. Editors select a public action key and can optionally provide an Access Gate area. The frontend renders a POST button, so the page itself does not need to be behind the gate.

## Zapier

There are two Zapier paths:

- **Webhook preset**: add an HTTP webhook destination using a Zapier Catch Hook URL.
- **Native Zapier integration**: use `integrations/zapier/capell-public-actions` with a Capell integration token.

Zapier endpoints:

- `GET /api/public-actions/zapier/me`
- `GET /api/public-actions/zapier/actions`
- `POST /api/public-actions/zapier/actions/{action}/submissions`
- `GET /api/public-actions/zapier/submissions`

Expose only actions with `settings.zapier_enabled` or `settings.api_enabled`.

## Form Builder

Form Builder is optional. Map form handles or IDs to public action keys in config:

```php
'form_builder' => [
    'mappings' => [
        'contact' => 'lead.capture',
        'id:12' => 'demo.request',
    ],
],
```
