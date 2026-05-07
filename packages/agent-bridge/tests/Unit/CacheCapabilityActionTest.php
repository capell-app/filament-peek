<?php

declare(strict_types=1);

use Capell\AgentBridge\Actions\Cache\ClearCapellCacheCapabilityAction;
use Capell\AgentBridge\Data\CapabilityData;
use Capell\AgentBridge\Data\CapabilityInvocationData;
use Capell\AgentBridge\Enums\CapabilityRiskEnum;
use Capell\AgentBridge\Enums\CapabilityServerEnum;
use Capell\AgentBridge\Tests\Fixtures\FakeCapabilityAction;

it('previews registered cache clear commands before executing them', function (): void {
    $result = (new ClearCapellCacheCapabilityAction)->preview(cacheInvocation());

    expect($result->ok)->toBeTrue()
        ->and($result->message)->toBe('Capell cache clear commands will be run if they are registered in this application.')
        ->and($result->data['commands'])->toContain('cache:clear');
});

function cacheInvocation(): CapabilityInvocationData
{
    return new CapabilityInvocationData(
        capability: new CapabilityData(
            key: 'capell.cache.clear',
            name: 'Clear cache',
            description: 'Clear cache capability.',
            scope: 'capell.cache.clear',
            server: CapabilityServerEnum::Site,
            risk: CapabilityRiskEnum::High,
            actionClass: FakeCapabilityAction::class,
        ),
        payload: [],
    );
}
