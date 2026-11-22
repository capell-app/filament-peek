<?php

declare(strict_types=1);

namespace Capell\FilamentPeek\Actions;

use Capell\Core\Models\Page;
use Capell\FilamentPeek\Concerns\ResolvesPreviewContext;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsFake;
use Lorisleiva\Actions\Concerns\AsObject;

/**
 * Drops every outstanding preview snapshot for a page, so a signed preview URL
 * cannot keep showing form state from before the page was saved.
 */
final class RevokePagePreviewSnapshotsAction
{
    use AsFake;
    use AsObject;
    use ResolvesPreviewContext;

    public function handle(Page $page): int
    {
        $store = Cache::store($this->previewCacheStore());
        $indexKey = $this->pageSnapshotIndexCacheKey($this->modelIntKey($page));
        $tokens = $store->get($indexKey);

        if (! is_array($tokens)) {
            return 0;
        }

        $revoked = 0;

        foreach ($tokens as $token) {
            if (is_string($token) && $store->forget($this->snapshotCacheKey($token))) {
                $revoked++;
            }
        }

        $store->forget($indexKey);

        return $revoked;
    }
}
