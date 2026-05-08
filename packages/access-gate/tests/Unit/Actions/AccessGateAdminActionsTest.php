<?php

declare(strict_types=1);

use Capell\AccessGate\Actions\ExpireRegistrationAction;
use Capell\AccessGate\Actions\RejectRegistrationAction;
use Capell\AccessGate\Actions\RevokeAccessGateBrowserTokenRecordAction;
use Capell\AccessGate\Actions\RevokeAccessGateGrantAction;
use Capell\AccessGate\Actions\UpdateAccessGateAreaStatusAction;
use Capell\AccessGate\Enums\AccessAreaStatus;
use Capell\AccessGate\Enums\BrowserTokenStatus;
use Capell\AccessGate\Enums\EventType;
use Capell\AccessGate\Enums\GrantStatus;
use Capell\AccessGate\Enums\RegistrationStatus;
use Capell\AccessGate\Models\Area;
use Capell\AccessGate\Models\BrowserToken;
use Capell\AccessGate\Models\Event;
use Capell\AccessGate\Models\Grant;
use Capell\AccessGate\Models\Registration;
use Capell\AccessGate\Tests\TestCase;

uses(TestCase::class);

it('rejects pending registrations and records an event', function (): void {
    $registration = Registration::factory()->create();

    $rejected = RejectRegistrationAction::run($registration, rejectedByUserId: 42);

    expect($rejected->status)->toBe(RegistrationStatus::Rejected)
        ->and($rejected->rejected_at)->not->toBeNull()
        ->and(Event::query()->where('type', EventType::RegistrationRejected)->where('user_id', 42)->exists())->toBeTrue();
});

it('expires unclaimed registrations', function (): void {
    $registration = Registration::factory()->create([
        'status' => RegistrationStatus::Approved,
        'approved_at' => now(),
    ]);

    $expired = ExpireRegistrationAction::run($registration);

    expect($expired->status)->toBe(RegistrationStatus::Expired)
        ->and($expired->expired_at)->not->toBeNull();
});

it('revokes grants and active browser sessions', function (): void {
    $grant = Grant::factory()->create();
    $browserToken = BrowserToken::factory()->for($grant, 'grant')->create();

    $revoked = RevokeAccessGateGrantAction::run($grant, revokedByUserId: 42);

    expect($revoked->status)->toBe(GrantStatus::Revoked)
        ->and($browserToken->refresh()->status)->toBe(BrowserTokenStatus::Revoked)
        ->and(Event::query()->where('type', EventType::GrantRevoked)->where('user_id', 42)->exists())->toBeTrue();
});

it('revokes a browser token record and records an event', function (): void {
    $browserToken = BrowserToken::factory()->create();

    $revoked = RevokeAccessGateBrowserTokenRecordAction::run($browserToken);

    expect($revoked->status)->toBe(BrowserTokenStatus::Revoked)
        ->and($revoked->revoked_at)->not->toBeNull()
        ->and(Event::query()->where('type', EventType::BrowserTokenRevoked)->exists())->toBeTrue();
});

it('updates access area status', function (): void {
    $area = Area::factory()->create([
        'status' => AccessAreaStatus::Active,
    ]);

    $updated = UpdateAccessGateAreaStatusAction::run($area, AccessAreaStatus::Paused);

    expect($updated->status)->toBe(AccessAreaStatus::Paused);
});
