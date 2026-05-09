<?php

declare(strict_types=1);

return [
    'route_prefix' => env('CAPELL_PUBLIC_ACTIONS_ROUTE_PREFIX', 'actions'),
    'api_route_prefix' => env('CAPELL_PUBLIC_ACTIONS_API_ROUTE_PREFIX', 'api/public-actions'),
    'queue' => env('CAPELL_PUBLIC_ACTIONS_QUEUE', 'default'),
    'webhook_timeout_seconds' => env('CAPELL_PUBLIC_ACTIONS_WEBHOOK_TIMEOUT', 10),
    'allow_insecure_webhook_urls' => env('CAPELL_PUBLIC_ACTIONS_ALLOW_INSECURE_WEBHOOK_URLS', false),
    'allow_private_webhook_urls' => env('CAPELL_PUBLIC_ACTIONS_ALLOW_PRIVATE_WEBHOOK_URLS', false),
    'submit_rate_limit' => 'public-actions-submit',
    'api_rate_limit' => 'public-actions-api',
    'tables' => [
        'actions' => 'public_actions',
        'destinations' => 'public_action_destinations',
        'submissions' => 'public_action_submissions',
        'dispatch_attempts' => 'public_action_dispatch_attempts',
        'integration_tokens' => 'public_action_integration_tokens',
    ],
    'adapters' => [
        'presets' => [
            'generic' => [
                'adapter' => 'http_webhook',
                'method' => 'POST',
                'expects_json' => true,
            ],
            'zapier' => [
                'adapter' => 'http_webhook',
                'method' => 'POST',
                'expects_json' => true,
            ],
            'pipedream' => [
                'adapter' => 'http_webhook',
                'method' => 'POST',
                'expects_json' => true,
            ],
            'n8n' => [
                'adapter' => 'http_webhook',
                'method' => 'POST',
                'expects_json' => true,
            ],
            'make' => [
                'adapter' => 'http_webhook',
                'method' => 'POST',
                'expects_json' => true,
            ],
        ],
    ],
];
