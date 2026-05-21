<?php

declare(strict_types=1);

use Capell\AccessGate\Filament\Resources\AccessAreas\AccessAreaResource;
use Capell\AccessGate\Filament\Resources\BrowserTokens\BrowserTokenResource;
use Capell\AccessGate\Filament\Resources\ClaimTokens\ClaimTokenResource;
use Capell\AccessGate\Filament\Resources\Events\AccessGateEventResource;
use Capell\AccessGate\Filament\Resources\Grants\GrantResource;
use Capell\AccessGate\Filament\Resources\Registrations\RegistrationResource;
use Capell\AccessGate\Health\AccessGateHealthCheck;
use Capell\AccessGate\Models\Area;
use Capell\AccessGate\Models\BrowserToken;
use Capell\AccessGate\Models\ClaimToken;
use Capell\AccessGate\Models\Event;
use Capell\AccessGate\Models\Grant;
use Capell\AccessGate\Models\Registration;
use Capell\Core\Facades\CapellCore;
use Filament\Schemas\Schema;

it('declares resource models pages schemas and tables', function (): void {
    CapellCore::forcePackageInstalled('capell-app/access-gate');

    expect(AccessAreaResource::getModel())->toBe(Area::class)
        ->and(RegistrationResource::getModel())->toBe(Registration::class)
        ->and(GrantResource::getModel())->toBe(Grant::class)
        ->and(ClaimTokenResource::getModel())->toBe(ClaimToken::class)
        ->and(BrowserTokenResource::getModel())->toBe(BrowserToken::class)
        ->and(AccessGateEventResource::getModel())->toBe(Event::class)
        ->and(AccessAreaResource::getPages())->toHaveKeys(['index', 'create', 'edit'])
        ->and(RegistrationResource::getPages())->toHaveKey('index')
        ->and(AccessAreaResource::shouldRegisterNavigation())->toBeTrue()
        ->and(AccessGateHealthCheck::compatibleCapellApiVersion())->toBe('^4.0')
        ->and(GrantResource::getPages())->toHaveKey('index')
        ->and(ClaimTokenResource::getPages())->toHaveKey('index')
        ->and(BrowserTokenResource::getPages())->toHaveKey('index')
        ->and(AccessGateEventResource::getPages())->toHaveKey('index');

    expect(AccessAreaResource::form(Schema::make()))->toBeInstanceOf(Schema::class)
        ->and(RegistrationResource::form(Schema::make()))->toBeInstanceOf(Schema::class);
});

it('reports cookie configuration failures as json doctor output', function (): void {
    config()->set('access-gate.cookies.browser_token.same_site', 'none');
    config()->set('access-gate.cookies.browser_token.secure', false);

    $this->artisan('capell:access-gate-doctor', ['--json' => true])
        ->assertFailed()
        ->expectsOutputToContain('"status": "failed"');
});

it('reports invalid same site cookie configuration', function (): void {
    config()->set('access-gate.cookies.browser_token.same_site', 'invalid');

    $this->artisan('capell:access-gate-doctor', ['--json' => true])
        ->assertFailed()
        ->expectsOutputToContain('"status": "failed"');
});
