<?php

declare(strict_types=1);

namespace Capell\Redirects\Support;

use Capell\Core\Models\PageUrl;
use Capell\Redirects\Contracts\RedirectRecorder;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class PageUrlRedirectRecorder implements RedirectRecorder
{
    public function recordHit(PageUrl $pageUrl): void
    {
        $pageUrl->newQuery()
            ->whereKey($pageUrl->getKey())
            ->update([
                'hit_count' => DB::raw('hit_count + 1'),
                'last_hit_at' => CarbonImmutable::now(),
            ]);
    }
}
