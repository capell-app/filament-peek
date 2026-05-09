# Email Studio

Status: **In development, schema-owning** · Kind: **package** · Tier: **premium** · Bundle: **communications** · Contexts: **admin, frontend, queue** · Product group: **Capell Communications**

Email Studio is Capell's transactional email centre. It gives packages a common way to register templates, render site-aware variants, send through configured providers, and keep an audit trail of what happened after the send.

It is not a newsletter package. Newsletter and audience tools own subscribers, imports, segments, consent, and bulk campaign delivery. Email Studio owns the operational email layer: form confirmations, account messages, editorial notifications, delivery events, replies, suppressions, and the diagnostics needed when a client asks whether an email was sent.

## What This Package Adds

- Reusable email templates with site-scoped variants, locale fallback, declared variables, subject, preview text, HTML, and plain text bodies.
- Delivery profiles for sender identity, reply-to addresses, provider type, tracking defaults, and provider settings.
- `SendEmailAction` as the canonical entrypoint for Capell packages that need audited email.
- Queue-first delivery through `SendEmailJob`, with immediate delivery available for tests and controlled workflows.
- Send recording across messages and recipients, including queued, sent, failed, partially failed, and suppressed states.
- Suppression checks before queueing and again before provider handoff.
- Provider adapter contracts with fake, SMTP, and Postmark adapters in the first slice.
- Typed Data objects for addresses, headers, rendering context, send commands, provider results, webhook events, and inbound replies.

## Current Implementation

The current package slice includes the data model, factories, provider registration, template registration, rendering, template variant resolution, suppression actions, the send pipeline, delivery handoff, and focused tests for the high-risk paths.

The next implementation slices will add webhook event recording, inbound reply recording, open/click tracking routes, retention redaction, Filament admin resources, diagnostics, and package integrations such as FormBuilder confirmation emails.

## Why It Matters

For developers, Email Studio removes the need for each package to build its own mailer conventions. Packages pass a `SendEmailData` object into one Action and get rendering, suppression checks, queue dispatch, provider selection, and audit records through the same path.

For teams, it creates one place to answer practical support questions:

- Which template was used?
- Which profile sent it?
- Who received it?
- Was the recipient suppressed?
- Did the provider accept or reject the message?
- Which payload and context were used at send time?

That is the part clients pay for. Sending an email is easy; proving what happened later is where the product value sits.

## Technical Shape

- `EmailStudioServiceProvider` registers config, translations, routes, migrations, models, and provider adapters.
- `AdminServiceProvider` and `FrontendServiceProvider` reserve the admin and public route surfaces for later slices.
- `EmailTemplateRegistry` stores package-owned template registrations.
- `EmailVariableRenderer` performs controlled `{{ variable }}` substitution using declared template variables.
- `EmailProfileResolver` resolves a requested profile or the best default profile for the site scope.
- `EmailProviderRegistry` isolates provider adapters from the send pipeline.
- `SendEmailAction` creates the message and recipient records, renders the selected variant, applies suppression state, and queues delivery.
- `DeliverEmailMessageAction` rechecks suppressions, calls the provider adapter, and records recipient/message outcomes.

## Provider Support

| Provider | Status  | Notes                                                                         |
| -------- | ------- | ----------------------------------------------------------------------------- |
| Fake     | Ready   | Deterministic IDs for tests and local diagnostics.                            |
| SMTP     | Ready   | Uses Laravel Mail and the selected mailer from profile settings.              |
| Postmark | Ready   | Uses the `postmark` mailer by default, or a profile-specific mailer override. |
| Mailgun  | Planned | Reserved for a production adapter slice.                                      |
| SES      | Planned | Reserved for a production adapter slice.                                      |
| Resend   | Planned | Reserved for a production adapter slice.                                      |

## Quick Start

```bash
composer require capell-app/email-studio
php artisan migrate
```

Create a default `EmailProfile`, an approved `EmailTemplate`, and at least one active `EmailTemplateVariant`. Then send through the Action:

```php
use Capell\EmailStudio\Actions\SendEmailAction;
use Capell\EmailStudio\Data\EmailAddressData;
use Capell\EmailStudio\Data\EmailHeaderData;
use Capell\EmailStudio\Data\SendEmailData;
use Spatie\LaravelData\DataCollection;

$message = SendEmailAction::run(new SendEmailData(
    templateKey: 'forms.confirmation',
    to: new DataCollection(EmailAddressData::class, [
        new EmailAddressData('customer@example.com', 'Customer Name'),
    ]),
    cc: new DataCollection(EmailAddressData::class, []),
    bcc: new DataCollection(EmailAddressData::class, []),
    siteId: 12,
    siteScopeKey: 'site:12',
    emailProfileId: null,
    variables: ['name' => 'Customer Name'],
    headers: new DataCollection(EmailHeaderData::class, []),
    triggeredByType: 'form_submission',
    triggeredById: 44,
    queue: true,
    locale: 'en',
));
```

## Boundaries

- Email Studio does not manage newsletter subscribers or audience imports.
- Email Studio does not send bulk campaigns in the first slice.
- Email Studio does not expose admin/editor state in public frontend output.
- Public tracking and webhook routes must use opaque tokens and generic invalid-token responses when they are implemented.
- Attachments are intentionally out of v1 until retention and privacy rules are settled.

## Common Pitfalls

- Only approved templates can be used for production sends.
- Only active variants are resolved.
- A send with no recipients fails before creating a message record.
- Suppressions are checked twice because an address can be suppressed after queueing and before delivery.
- Provider-level failures and adapter exceptions are recorded on the Email Studio message and recipients.
- If no locale is provided to `SendEmailData`, the send uses Laravel's current locale and falls back to a neutral variant.

## Verification

Run the focused package tests:

```bash
vendor/bin/pest packages/email-studio/tests --configuration=phpunit.xml
```

Run static analysis for package source:

```bash
vendor/bin/phpstan analyse packages/email-studio/src --memory-limit=-1 --configuration=phpstan.source.neon
```

## Next Steps

- [docs/overview.md](docs/overview.md)
- [docs/email-studio-api.md](docs/email-studio-api.md)
- [docs/email-studio-database.md](docs/email-studio-database.md)
