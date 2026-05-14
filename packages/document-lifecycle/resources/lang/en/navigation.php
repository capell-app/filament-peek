<?php

declare(strict_types=1);

return [
    'group' => 'Document Lifecycle',
    'document' => 'document',
    'documents' => 'Controlled documents',
    'fields' => [
        'key' => 'Key',
        'title' => 'Title',
        'status' => 'Status',
        'metadata' => 'Metadata',
        'publications' => 'Publications',
        'version' => 'Version',
        'hash' => 'Hash',
        'revision' => 'Revision',
        'published_at' => 'Published',
        'context' => 'Context',
        'acceptor' => 'Acceptor',
        'accepted_at' => 'Accepted',
        'updated_at' => 'Updated',
    ],
    'relations' => [
        'publications' => 'Publications',
        'acceptances' => 'Acceptances',
    ],
    'status' => [
        'draft' => 'Draft',
        'active' => 'Active',
        'archived' => 'Archived',
    ],
];
