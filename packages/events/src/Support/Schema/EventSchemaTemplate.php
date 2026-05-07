<?php

declare(strict_types=1);

namespace Capell\Events\Support\Schema;

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Events\Actions\BuildEventSchemaAction;
use Capell\Events\Models\Event;
use Capell\Events\Models\EventOccurrence;
use Capell\SeoSuite\Contracts\SchemaTemplate;

class EventSchemaTemplate implements SchemaTemplate
{
    public function build(Page $page, Site $site, Language $language): array
    {
        if (! $page->pageable instanceof Event) {
            return [];
        }

        $occurrence = EventOccurrence::query()
            ->where('event_id', $page->pageable->getKey())
            ->public()
            ->ordered()
            ->first();

        if (! $occurrence instanceof EventOccurrence) {
            return [];
        }

        return BuildEventSchemaAction::run($occurrence);
    }

    public function requiredFields(Page $page, Site $site, Language $language): array
    {
        return ['@type', 'name', 'startDate', 'eventStatus', 'eventAttendanceMode'];
    }
}
