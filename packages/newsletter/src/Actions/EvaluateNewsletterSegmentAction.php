<?php

declare(strict_types=1);

namespace Capell\Newsletter\Actions;

use Capell\Newsletter\Enums\SegmentType;
use Capell\Newsletter\Models\Segment;
use Capell\Newsletter\Models\Subscriber;
use Illuminate\Database\Eloquent\Builder;
use Lorisleiva\Actions\Concerns\AsAction;

class EvaluateNewsletterSegmentAction
{
    use AsAction;

    /**
     * @return Builder<Subscriber>
     */
    public function handle(Segment $segment): Builder
    {
        $query = Subscriber::query()->where('site_id', $segment->site_id);

        if ($segment->type === SegmentType::Static) {
            return $query->whereHas('segments', function (Builder $segmentQuery) use ($segment): void {
                $segmentQuery->whereKey($segment->getKey());
            });
        }

        $filters = is_array($segment->filters) ? $segment->filters : [];

        if (isset($filters['status']) && is_string($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['source_form_handle']) && is_string($filters['source_form_handle'])) {
            $query->where('source_form_handle', $filters['source_form_handle']);
        }

        if (isset($filters['created_from']) && is_string($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (isset($filters['created_until']) && is_string($filters['created_until'])) {
            $query->whereDate('created_at', '<=', $filters['created_until']);
        }

        if (isset($filters['tag_ids']) && is_array($filters['tag_ids'])) {
            $tagIds = array_map(static fn (mixed $tagId): int => (int) $tagId, $filters['tag_ids']);
            $query->whereHas('tags', function (Builder $tagQuery) use ($tagIds): void {
                $tagQuery->whereIn('tags.id', $tagIds);
            });
        }

        if (isset($filters['provider_sync_status']) && is_string($filters['provider_sync_status'])) {
            $query->whereHas('providerSubscribers', function (Builder $providerSubscriberQuery) use ($filters): void {
                $providerSubscriberQuery->where('remote_status', $filters['provider_sync_status']);
            });
        }

        return $query;
    }
}
