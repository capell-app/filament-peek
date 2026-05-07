<?php

declare(strict_types=1);

return [
    'subscriber_status' => [
        'pending' => 'Pending',
        'subscribed' => 'Subscribed',
        'unsubscribed' => 'Unsubscribed',
        'suppressed' => 'Suppressed',
        'bounced' => 'Bounced',
        'complained' => 'Complained',
    ],
    'consent_event_type' => [
        'form_capture' => 'Form capture',
        'double_opt_in_requested' => 'Double opt-in requested',
        'double_opt_in_confirmed' => 'Double opt-in confirmed',
        'unsubscribed' => 'Unsubscribed',
        'imported' => 'Imported',
        'admin_updated' => 'Admin updated',
        'provider_webhook' => 'Provider webhook',
    ],
    'confirmation_mode' => [
        'capell_owned' => 'Capell owned',
        'provider_owned' => 'Provider owned',
    ],
    'provider' => [
        'mailchimp' => 'Mailchimp',
        'kit' => 'Kit / ConvertKit',
        'campaign_monitor' => 'Campaign Monitor',
        'fake' => 'Fake',
    ],
    'auth_type' => [
        'api_key' => 'API key',
        'oauth' => 'OAuth',
    ],
    'sync_status' => [
        'pending' => 'Pending',
        'running' => 'Running',
        'succeeded' => 'Succeeded',
        'failed' => 'Failed',
        'retry_scheduled' => 'Retry scheduled',
    ],
    'resubscribe_policy' => [
        'require_double_opt_in' => 'Require double opt-in',
        'allow_with_consent' => 'Allow with consent',
        'block_suppressed_only' => 'Block suppressed only',
    ],
    'segment_type' => [
        'static' => 'Static',
        'saved_filter' => 'Saved filter',
    ],
];
