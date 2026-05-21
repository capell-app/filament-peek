<?php

declare(strict_types=1);

namespace Capell\Events\Policies;

use Illuminate\Database\Eloquent\Model;
use Override;

final class EventOccurrencePolicy extends AbstractEventResourcePolicy
{
    protected static function subject(): string
    {
        return 'EventOccurrence';
    }

    #[Override]
    protected function recordSiteId(Model $record): ?int
    {
        $record->loadMissing('event');

        $siteId = $record->getRelation('event')?->getAttribute('site_id');

        return is_numeric($siteId) ? (int) $siteId : null;
    }
}
