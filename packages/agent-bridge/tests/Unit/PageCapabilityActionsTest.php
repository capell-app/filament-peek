<?php

declare(strict_types=1);

use Capell\AgentBridge\Actions\Pages\CreateDraftPageCapabilityAction;
use Capell\AgentBridge\Actions\Pages\DisablePageCapabilityAction;
use Capell\AgentBridge\Actions\Pages\InspectPagePublishingReadinessCapabilityAction;
use Capell\AgentBridge\Actions\Pages\UpdateDraftPageCapabilityAction;
use Capell\AgentBridge\Data\CapabilityData;
use Capell\AgentBridge\Data\CapabilityInvocationData;
use Capell\AgentBridge\Enums\CapabilityRiskEnum;
use Capell\AgentBridge\Enums\CapabilityServerEnum;
use Capell\AgentBridge\Tests\Fixtures\FakeCapabilityAction;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

it('previews draft page creation with the validated payload', function (): void {
    $payload = [
        'name' => 'Campaign landing page',
        'site_id' => 1,
        'blueprint_id' => 2,
        'layout_id' => 3,
        'meta' => ['hidden' => true],
    ];

    $result = (new CreateDraftPageCapabilityAction)->preview(pageInvocation($payload));

    expect($result->ok)->toBeTrue()
        ->and($result->message)->toBe('A new unpublished page record will be created.')
        ->and($result->data['page'])->toBe($payload);
});

it('rejects draft page creation payloads without required page fields', function (): void {
    (new CreateDraftPageCapabilityAction)->preview(pageInvocation([
        'name' => 'Missing page relationships',
    ]));
})->throws(ValidationException::class);

it('rejects draft page creation without an authenticated user', function (): void {
    (new CreateDraftPageCapabilityAction)->preview(pageInvocation([
        'name' => 'Campaign landing page',
        'site_id' => 1,
        'blueprint_id' => 2,
        'layout_id' => 3,
    ], user: false));
})->throws(ValidationException::class);

it('rejects draft page creation for unassigned sites', function (): void {
    (new CreateDraftPageCapabilityAction)->preview(pageInvocation([
        'name' => 'Campaign landing page',
        'site_id' => 99,
        'blueprint_id' => 2,
        'layout_id' => 3,
    ], pageCapabilityUser(collect([1]))));
})->throws(ValidationException::class);

it('previews safe draft page updates without exposing the page id as a change', function (): void {
    $result = (new UpdateDraftPageCapabilityAction)->preview(pageInvocation([
        'page_id' => 10,
        'name' => 'Updated campaign page',
        'meta' => ['hidden' => false],
    ]));

    expect($result->ok)->toBeTrue()
        ->and($result->message)->toBe('The selected page will be updated with safe editable fields.')
        ->and($result->data['page_id'])->toBe(10)
        ->and($result->data['changes'])->toBe([
            'name' => 'Updated campaign page',
            'meta' => ['hidden' => false],
        ]);
});

it('rejects draft page update payloads without a page id', function (): void {
    (new UpdateDraftPageCapabilityAction)->preview(pageInvocation([
        'name' => 'Missing page id',
    ]));
})->throws(ValidationException::class);

it('previews disabling a page by ending its visibility window', function (): void {
    $result = (new DisablePageCapabilityAction)->preview(pageInvocation([
        'page_id' => 22,
    ]));

    expect($result->ok)->toBeTrue()
        ->and($result->message)->toBe('The page visibility window will be ended immediately.')
        ->and($result->data['page_id'])->toBe(22)
        ->and($result->data['visible_until'])->toBeString();
});

it('rejects disable page payloads without a page id', function (): void {
    (new DisablePageCapabilityAction)->preview(pageInvocation([]));
})->throws(ValidationException::class);

it('rejects readiness inspection payloads without a page id before querying pages', function (): void {
    (new InspectPagePublishingReadinessCapabilityAction)->preview(pageInvocation([]));
})->throws(ValidationException::class);

/**
 * @param  array<string, mixed>  $payload
 */
function pageInvocation(array $payload, AuthenticatableContract|false|null $user = null): CapabilityInvocationData
{
    if ($user === null) {
        $user = pageCapabilityUser(collect([(int) ($payload['site_id'] ?? 1)]));
    }

    return new CapabilityInvocationData(
        capability: new CapabilityData(
            key: 'capell.pages.test',
            name: 'Page test',
            description: 'Page test capability.',
            scope: 'capell.pages.test',
            server: CapabilityServerEnum::Site,
            risk: CapabilityRiskEnum::High,
            actionClass: FakeCapabilityAction::class,
        ),
        payload: $payload,
        user: $user === false ? null : $user,
    );
}

/**
 * @param  Collection<int, int>  $assignedSiteIds
 */
function pageCapabilityUser(Collection $assignedSiteIds, bool $isGlobalAdmin = false): AuthenticatableContract
{
    return new readonly class($assignedSiteIds, $isGlobalAdmin) implements AuthenticatableContract
    {
        /**
         * @param  Collection<int, int>  $assignedSiteIds
         */
        public function __construct(
            private Collection $assignedSiteIds,
            private bool $isGlobalAdmin,
        ) {}

        /**
         * @return Collection<int, int>
         */
        public function getAssignedSiteIds(): Collection
        {
            return $this->assignedSiteIds;
        }

        public function hasRole(string $role): bool
        {
            return $this->isGlobalAdmin && $role === config('capell.roles.super_admin', 'super_admin');
        }

        public function getAuthIdentifierName(): string
        {
            return 'id';
        }

        public function getAuthIdentifier(): int
        {
            return 1;
        }

        public function getAuthPasswordName(): string
        {
            return 'password';
        }

        public function getAuthPassword(): string
        {
            return '';
        }

        public function getRememberToken(): ?string
        {
            return null;
        }

        public function setRememberToken(mixed $value): void
        {
            //
        }

        public function getRememberTokenName(): string
        {
            return 'remember_token';
        }
    };
}
