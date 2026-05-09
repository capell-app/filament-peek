<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Actions;

use Capell\EmailStudio\Enums\SuppressionReason;
use Capell\EmailStudio\Models\EmailSuppression;
use Capell\EmailStudio\Support\EmailAddressNormalizer;
use Lorisleiva\Actions\Concerns\AsAction;

class SuppressEmailAddressAction
{
    use AsAction;

    public function handle(
        string $email,
        SuppressionReason $reason = SuppressionReason::Manual,
        ?int $siteId = null,
        string $siteScopeKey = 'global',
        string $source = 'manual',
        ?string $notes = null,
    ): EmailSuppression {
        $normalizer = resolve(EmailAddressNormalizer::class);
        $normalizedEmail = $normalizer->normalize($email);

        /** @var EmailSuppression $suppression */
        $suppression = EmailSuppression::query()->updateOrCreate(
            [
                'site_scope_key' => $siteScopeKey,
                'email_hash' => $normalizer->hash($email),
                'reason' => $reason,
                'source' => $source,
            ],
            [
                'site_id' => $siteId,
                'email' => $email,
                'normalized_email' => $normalizedEmail,
                'notes' => $notes,
                'suppressed_at' => now()->toImmutable(),
                'released_at' => null,
            ],
        );

        return $suppression;
    }
}
