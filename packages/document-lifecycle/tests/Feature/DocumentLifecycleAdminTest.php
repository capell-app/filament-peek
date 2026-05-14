<?php

declare(strict_types=1);

use Capell\DocumentLifecycle\Enums\DocumentStatusEnum;
use Capell\DocumentLifecycle\Filament\Resources\Documents\DocumentResource;
use Capell\DocumentLifecycle\Models\Document;
use Capell\Tests\Support\Concerns\CreatesAdminUser;

use function Pest\Laravel\get;

uses(CreatesAdminUser::class);

it('exposes controlled documents in the admin surface', function (): void {
    test()->actingAsAdmin();

    $document = Document::query()->create([
        'key' => 'terms',
        'title' => 'Terms of Service',
        'status' => DocumentStatusEnum::Active,
        'metadata' => ['source' => 'test'],
    ]);

    get(DocumentResource::getUrl())
        ->assertOk()
        ->assertSee('Terms of Service');

    get(DocumentResource::getUrl('edit', ['record' => $document]))
        ->assertOk()
        ->assertSee('terms');
});
