<?php

declare(strict_types=1);

namespace Capell\Newsletter\Support;

class CapellNewsletterManager
{
    /**
     * @return array<int, string>
     */
    public static function getMigrations(): array
    {
        return [
            'create_newsletter_subscribers_table',
            'create_newsletter_provider_connections_table',
            'create_newsletter_consent_events_table',
            'create_newsletter_public_tokens_table',
            'create_newsletter_form_mappings_table',
            'create_newsletter_provider_audiences_table',
            'create_newsletter_provider_interest_mappings_table',
            'create_newsletter_provider_subscribers_table',
            'create_newsletter_sync_attempts_table',
            'create_newsletter_segments_table',
            'create_newsletter_import_batches_table',
        ];
    }
}
