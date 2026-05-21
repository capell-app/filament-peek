<?php

declare(strict_types=1);

namespace Capell\Events\Policies;

use Illuminate\Database\Eloquent\Model;
use Override;

final class EventRegistrationPolicy extends AbstractEventResourcePolicy
{
    protected static function subject(): string
    {
        return 'EventRegistration';
    }

    #[Override]
    protected function recordSiteId(Model $record): ?int
    {
        $record->loadMissing('occurrence.event');

        $siteId = $record->getRelation('occurrence')?->getRelation('event')?->getAttribute('site_id');

        return is_numeric($siteId) ? (int) $siteId : null;
    }
}
