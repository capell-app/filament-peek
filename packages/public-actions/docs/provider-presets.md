# Public Actions Provider Presets

Public Actions starts with one transport: `http_webhook`.

Provider presets are thin defaults over that transport. Capell sends a JSON payload to the configured endpoint, records the dispatch attempt, and leaves downstream workflow logic to the provider.

## Presets

- `generic`: POST JSON to any HTTP endpoint.
- `zapier`: POST JSON to a Zapier Catch Hook URL.
- `pipedream`: POST JSON to a Pipedream HTTP trigger URL.
- `n8n`: POST JSON to an n8n production webhook URL. Add auth headers on the destination when the n8n webhook requires them.
- `make`: POST JSON to a Make custom webhook URL.

## Native Zapier Integration

The `zapier` preset is only the fast outbound Catch Hook path. The native Zapier integration is separate and will live under `integrations/zapier/capell-public-actions`.

Use the preset when a site only needs to send submission data into an existing Zap. Use the native integration when Zapier should authenticate to Capell, poll Capell submissions as triggers, or submit Capell Public Actions as Zap actions.

## Payload Shape

Every preset sends the same JSON structure:

```json
{
    "action": {
        "key": "preview-access",
        "name": "Preview access"
    },
    "submission": {
        "id": "123",
        "status": "handled",
        "submitted_at": "2026-05-09T12:00:00+00:00",
        "source_type": "button",
        "source_id": "hero"
    },
    "payload": {
        "email": "person@example.test"
    },
    "metadata": {
        "ip_hash": "sha256-hash",
        "user_agent": "Browser user agent",
        "url": "https://example.test/page"
    },
    "destination": {
        "name": "Zapier catch hook",
        "adapter": "http_webhook"
    }
}
```

Public Actions stores encrypted destination endpoints, secrets, headers, and submission payloads. Attempt summaries redact endpoint URLs, shared secrets, and configured header values before persistence.
