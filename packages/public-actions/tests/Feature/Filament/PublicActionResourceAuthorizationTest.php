<?php

declare(strict_types=1);

use Capell\PublicActions\Filament\Resources\Destinations\PublicActionDestinationResource;
use Capell\PublicActions\Filament\Resources\DispatchAttempts\PublicActionDispatchAttemptResource;
use Capell\PublicActions\Filament\Resources\IntegrationTokens\PublicActionIntegrationTokenResource;
use Capell\PublicActions\Filament\Resources\PublicActions\PublicActionResource;
use Capell\PublicActions\Filament\Resources\Submissions\PublicActionSubmissionResource;
use Capell\PublicActions\Models\PublicActionIntegrationToken;
use Capell\Tests\Support\Concerns\CreatesAdminUser;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Permission;

uses(CreatesAdminUser::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('requires view permissions for public actions admin resources', function (string $resourceClass, string $permission): void {
    test()->actingAsUser();

    /** @var class-string<\Filament\Resources\Resource> $resourceClass */
    expect($resourceClass::canAccess())->toBeFalse()
        ->and($resourceClass::canViewAny())->toBeFalse();

    Permission::findOrCreate($permission);
    test()->actingAs(test()->createUserWithPermission($permission));

    expect($resourceClass::canAccess())->toBeTrue()
        ->and($resourceClass::canViewAny())->toBeTrue();
})->with([
    'public actions' => [PublicActionResource::class, 'ViewAny:PublicAction'],
    'destinations' => [PublicActionDestinationResource::class, 'ViewAny:PublicActionDestination'],
    'submissions' => [PublicActionSubmissionResource::class, 'ViewAny:PublicActionSubmission'],
    'dispatch attempts' => [PublicActionDispatchAttemptResource::class, 'ViewAny:PublicActionDispatchAttempt'],
    'integration tokens' => [PublicActionIntegrationTokenResource::class, 'ViewAny:PublicActionIntegrationToken'],
]);

it('requires update permission to mutate public action integration tokens', function (): void {
    $token = PublicActionIntegrationToken::factory()->create();

    Permission::findOrCreate('ViewAny:PublicActionIntegrationToken');
    Permission::findOrCreate('Update:PublicActionIntegrationToken');

    test()->actingAs(test()->createUserWithPermission('ViewAny:PublicActionIntegrationToken'));

    expect(PublicActionIntegrationTokenResource::canEdit($token))->toBeFalse();

    test()->actingAs(test()->createUserWithPermission('Update:PublicActionIntegrationToken'));

    expect(PublicActionIntegrationTokenResource::canEdit($token))->toBeTrue();
});
