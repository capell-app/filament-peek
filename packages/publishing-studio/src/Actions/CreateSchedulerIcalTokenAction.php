<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\Admin\Support\SiteScope;
use Capell\Core\Models\Site;
use Capell\PublishingStudio\Enums\SchedulerIcalFeedScopeEnum;
use Capell\PublishingStudio\Models\SchedulerIcalToken;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateSchedulerIcalTokenAction
{
    use AsAction;

    public function handle(Authenticatable $owner, SchedulerIcalFeedScopeEnum $scope, ?int $siteId = null): string
    {
        if ($scope === SchedulerIcalFeedScopeEnum::Site) {
            $site = $siteId !== null ? Site::query()->find($siteId) : null;
            throw_if(! $site instanceof Site || ! SiteScope::actorCanUseSite($owner, $site), AuthorizationException::class);
        }

        $plainToken = Str::random(48);

        SchedulerIcalToken::query()->create([
            'token_hash' => hash('sha256', $plainToken),
            'scope' => $scope,
            'site_id' => $siteId,
            'owner_type' => $owner->getMorphClass(),
            'owner_id' => $owner->getKey(),
        ]);

        return $plainToken;
    }
}
