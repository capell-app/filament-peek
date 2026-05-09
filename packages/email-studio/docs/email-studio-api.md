# Email Studio API

Email Studio exposes Actions and Data objects as its package API. Controllers, Filament resources, listeners, and other packages should call these Actions instead of writing directly to the email tables.

## Registering Templates

Use `RegisterEmailTemplateAction` when a package owns a template definition.

```php
use Capell\EmailStudio\Actions\RegisterEmailTemplateAction;

RegisterEmailTemplateAction::run(
    key: 'forms.confirmation',
    name: 'Form confirmation',
    variables: ['name', 'submission_reference'],
    description: 'Sent after a successful form submission.',
    packageName: 'capell-app/form-builder',
    siteId: null,
    siteScopeKey: 'global',
);
```

Registrations are upserted by package, key, and site scope. They describe the expected template contract; the actual approved sendable content lives in `EmailTemplate` and `EmailTemplateVariant`.

## Sending Email

`SendEmailAction` is the canonical entrypoint.

```php
use Capell\EmailStudio\Actions\SendEmailAction;
use Capell\EmailStudio\Data\EmailAddressData;
use Capell\EmailStudio\Data\EmailHeaderData;
use Capell\EmailStudio\Data\SendEmailData;
use Spatie\LaravelData\DataCollection;

SendEmailAction::run(new SendEmailData(
    templateKey: 'forms.confirmation',
    to: new DataCollection(EmailAddressData::class, [
        new EmailAddressData('customer@example.com', 'Customer Name'),
    ]),
    cc: new DataCollection(EmailAddressData::class, []),
    bcc: new DataCollection(EmailAddressData::class, []),
    siteId: 12,
    siteScopeKey: 'site:12',
    emailProfileId: null,
    variables: [
        'name' => 'Customer Name',
        'submission_reference' => 'FS-1001',
    ],
    headers: new DataCollection(EmailHeaderData::class, []),
    triggeredByType: 'form_submission',
    triggeredById: 1001,
    queue: true,
    locale: 'en',
));
```

The Action:

- resolves a profile from the requested site scope;
- resolves an approved template;
- resolves an active variant for the requested or current locale;
- renders declared variables;
- creates the message and recipient rows;
- applies suppressions;
- dispatches `SendEmailJob` when queueing is enabled.

Pass `queue: false` only for tests or controlled workflows that need immediate delivery.

## Rendering A Variant

Use `RenderEmailTemplateAction` when the admin UI needs a preview.

```php
use Capell\EmailStudio\Actions\RenderEmailTemplateAction;
use Capell\EmailStudio\Data\EmailContextData;

$rendered = RenderEmailTemplateAction::run(
    variant: $variant,
    context: new EmailContextData(
        variables: ['name' => 'Customer Name'],
        preview: true,
    ),
);
```

Preview mode leaves missing markers visible. Production mode throws `EmailTemplateRenderingException` when a variable is missing or undeclared.

## Resolving Variants

Use `ResolveEmailTemplateVariantAction` to select the sendable content for a template.

```php
$variant = ResolveEmailTemplateVariantAction::run(
    template: $template,
    siteScopeKey: 'site:12',
    locale: 'en',
);
```

Resolution order:

1. requested site scope before global;
2. requested locale before neutral locale;
3. latest version among matching active variants.

When no locale is supplied, the resolver only returns neutral variants. `SendEmailAction` supplies Laravel's current locale by default.

## Suppressions

Use `SuppressEmailAddressAction` to add or reactivate a suppression.

```php
use Capell\EmailStudio\Actions\SuppressEmailAddressAction;
use Capell\EmailStudio\Enums\SuppressionReason;

SuppressEmailAddressAction::run(
    email: 'blocked@example.com',
    reason: SuppressionReason::Complaint,
    siteId: null,
    siteScopeKey: 'global',
    source: 'provider',
);
```

Use `CheckEmailSuppressionAction` when another integration needs to check before offering an email workflow.

```php
$suppressed = CheckEmailSuppressionAction::run('blocked@example.com', 'site:12');
```

Global suppressions apply to every site. Site-scoped suppressions apply only to that site scope.

## Provider Adapters

Providers implement `EmailProviderAdapter`.

```php
use Capell\EmailStudio\Contracts\EmailProviderAdapter;
use Capell\EmailStudio\Enums\EmailProviderType;
use Capell\EmailStudio\Support\EmailProviderRegistry;

resolve(EmailProviderRegistry::class)->register(
    EmailProviderType::Fake,
    new CustomProviderAdapter(),
);
```

Adapters return `ProviderSendResultData`. A provider-level failure should return `successful: false` and a failure reason. If the adapter throws, `DeliverEmailMessageAction` records the message and queued recipients as failed.

## Error Handling

`EmailStudioSendingException` is thrown when:

- no recipients are provided;
- no profile is available;
- no approved template is available;
- no active variant is available;
- a provider adapter is not registered.

Provider transport failures are recorded on the message and recipients instead of bubbling through as unrecorded queue failures.
