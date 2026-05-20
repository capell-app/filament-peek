<?php

declare(strict_types=1);

use Capell\AccessGate\Filament\Resources\AccessAreas\AccessAreaResource;
use Capell\AccessGate\Filament\Resources\BrowserTokens\BrowserTokenResource;
use Capell\AccessGate\Filament\Resources\ClaimTokens\ClaimTokenResource;
use Capell\AccessGate\Filament\Resources\Events\AccessGateEventResource;
use Capell\AccessGate\Filament\Resources\Grants\GrantResource;
use Capell\AccessGate\Filament\Resources\Registrations\RegistrationResource;

it('keeps access gate contained under websites navigation', function (): void {
    $navigationGroup = (string) __('capell-admin::navigation.group_websites');
    $parentItem = (string) __('capell-access-gate::filament.navigation_group');

    expect(AccessAreaResource::getNavigationGroup())->toBe($navigationGroup)
        ->and(AccessAreaResource::getNavigationLabel())->toBe($parentItem)
        ->and(AccessAreaResource::getNavigationSort())->toBe(30)
        ->and(AccessAreaResource::getNavigationParentItem())->toBeNull()
        ->and(RegistrationResource::getNavigationGroup())->toBe($navigationGroup)
        ->and(RegistrationResource::getNavigationParentItem())->toBe($parentItem)
        ->and(RegistrationResource::getNavigationSort())->toBe(31)
        ->and(GrantResource::getNavigationParentItem())->toBe($parentItem)
        ->and(GrantResource::getNavigationSort())->toBe(32)
        ->and(ClaimTokenResource::getNavigationParentItem())->toBe($parentItem)
        ->and(ClaimTokenResource::getNavigationSort())->toBe(33)
        ->and(BrowserTokenResource::getNavigationParentItem())->toBe($parentItem)
        ->and(BrowserTokenResource::getNavigationSort())->toBe(34)
        ->and(AccessGateEventResource::getNavigationParentItem())->toBe($parentItem)
        ->and(AccessGateEventResource::getNavigationSort())->toBe(35);
});
