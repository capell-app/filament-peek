<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Support;

use Capell\EmailStudio\Models\EmailProfile;
use Illuminate\Database\Eloquent\Builder;

class EmailProfileResolver
{
    public function resolve(string $siteScopeKey, ?int $emailProfileId = null): ?EmailProfile
    {
        $query = EmailProfile::query()
            ->whereIn('site_scope_key', [$siteScopeKey, 'global'])
            ->orderByRaw('case when site_scope_key = ? then 0 else 1 end', [$siteScopeKey]);

        if ($emailProfileId !== null) {
            return $query->whereKey($emailProfileId)->first();
        }

        return $query
            ->where('is_default', true)
            ->when(
                config('capell-email-studio.default_provider') !== null,
                fn (Builder $builder): Builder => $builder->orderByRaw(
                    'case when provider = ? then 0 else 1 end',
                    [(string) config('capell-email-studio.default_provider')],
                ),
            )
            ->first();
    }
}
