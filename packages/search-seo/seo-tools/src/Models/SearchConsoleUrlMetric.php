<?php

declare(strict_types=1);

namespace Capell\SeoTools\Models;

use Capell\Core\Models\Site;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchConsoleUrlMetric extends Model
{
    protected $guarded = [];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function scopeDecliningPages(Builder $query, int $siteId, int $limit = 10): Builder
    {
        return $query
            ->where('site_id', $siteId)
            ->where('click_delta', '<', 0)
            ->orderBy('click_delta')
            ->limit($limit);
    }

    protected function casts(): array
    {
        return [
            'window_start' => 'date',
            'window_end' => 'date',
            'ctr' => 'float',
            'average_position' => 'float',
            'previous_ctr' => 'float',
            'previous_average_position' => 'float',
            'position_delta' => 'float',
            'synced_at' => 'datetime',
        ];
    }
}
