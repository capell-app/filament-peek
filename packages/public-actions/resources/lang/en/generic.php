<?php

declare(strict_types=1);

return [
    'submitted' => 'Your request has been submitted.',
    'unavailable' => 'This action is not available.',
    'statuses' => [
        'action' => [
            'active' => 'Active',
            'paused' => 'Paused',
            'archived' => 'Archived',
        ],
        'destination' => [
            'active' => 'Active',
            'paused' => 'Paused',
        ],
        'submission' => [
            'received' => 'Received',
            'handled' => 'Handled',
            'failed' => 'Failed',
        ],
        'dispatch' => [
            'pending' => 'Pending',
            'succeeded' => 'Succeeded',
            'failed' => 'Failed',
            'retryable' => 'Retryable',
        ],
        'integration_provider' => [
            'zapier' => 'Zapier',
            'api' => 'API',
        ],
        'integration_ability' => [
            'list_actions' => 'List actions',
            'submit_actions' => 'Submit actions',
            'read_submissions' => 'Read submissions',
        ],
    ],
];
