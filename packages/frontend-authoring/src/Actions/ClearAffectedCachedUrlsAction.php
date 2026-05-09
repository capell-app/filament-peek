<?php

declare(strict_types=1);

namespace Capell\FrontendAuthoring\Actions;

use Capell\HtmlCache\Actions\ClearCachedUrlAction;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\Concerns\AsObject;

class ClearAffectedCachedUrlsAction
{
    use AsObject;

    /**
     * @param  list<string>  $urls
     */
    public function handle(Model $model, array $urls, string $currentUrl): int
    {
        $cleared = 0;

        foreach ($urls as $url) {
            if (ClearCachedUrlAction::run(
                url: $url,
                refresh: config('capell-admin.auto_refresh_cache') === true || $url === $currentUrl,
            )) {
                $cleared++;
            }
        }

        return $cleared;
    }
}
