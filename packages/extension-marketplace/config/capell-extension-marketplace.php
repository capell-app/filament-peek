<?php

declare(strict_types=1);

return [
    'enabled' => env('CAPELL_EXTENSION_MARKETPLACE_ENABLED', true),
    'instance' => [
        'id' => env('CAPELL_INSTANCE_ID'),
    ],
    'marketplace' => [
        'base_url' => env('CAPELL_MARKETPLACE_URL', 'https://capell.app/api'),
        'timeout_seconds' => 10,
        'cache_ttl_seconds' => 300,
        'webhook_url' => env('CAPELL_MARKETPLACE_WEBHOOK_URL'),
        'webhook_secret' => env('CAPELL_MARKETPLACE_WEBHOOK_SECRET'),
        'troubleshooting_url' => env('CAPELL_MARKETPLACE_TROUBLESHOOTING_URL', 'https://docs.capell.app/extensions/marketplace-heartbeat'),
        'hidden_composer_names' => [
            'capell-app/core',
            'capell-app/extension-marketplace',
        ],
    ],
];
