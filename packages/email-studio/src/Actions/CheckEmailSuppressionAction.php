<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Actions;

use Capell\EmailStudio\Models\EmailSuppression;
use Capell\EmailStudio\Support\EmailAddressNormalizer;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckEmailSuppressionAction
{
    use AsAction;

    public function handle(string $email, string $siteScopeKey = 'global'): bool
    {
        $emailHash = resolve(EmailAddressNormalizer::class)->hash($email);

        return EmailSuppression::query()
            ->where('email_hash', $emailHash)
            ->whereIn('site_scope_key', [$siteScopeKey, 'global'])
            ->whereNull('released_at')
            ->exists();
    }
}
