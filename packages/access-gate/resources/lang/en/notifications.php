<?php

declare(strict_types=1);

return [
    'request_received' => [
        'subject' => 'Access request received for :area',
        'greeting' => 'Access request received',
        'lines' => [
            'received' => 'We have received your request for :area.',
            'next' => 'If access is approved, you will receive a secure link by email.',
        ],
    ],
    'approved' => [
        'subject' => 'Your access is ready for :area',
        'greeting' => 'Your access is ready',
        'action' => 'Claim access',
        'lines' => [
            'approved' => 'Your request for :area has been approved.',
            'claim' => 'This link can only be used once. If it expires, request access again with the same email address.',
        ],
    ],
    'revoked' => [
        'subject' => 'Access changed for :area',
        'greeting' => 'Access changed',
        'lines' => [
            'revoked' => 'Your access to :area has been revoked.',
        ],
    ],
    'expired' => [
        'subject' => 'Access expired for :area',
        'greeting' => 'Access expired',
        'lines' => [
            'expired' => 'Your access to :area has expired.',
        ],
    ],
];
