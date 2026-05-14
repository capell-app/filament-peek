<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\PublishingStudio\Models\PublishingRevision;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\Concerns\AsAction;

final class ResolveLatestPublishingRevisionAction
{
    use AsAction;

    public function handle(Model $revisionable): ?PublishingRevision
    {
        return ListPublishingRevisionsAction::run($revisionable)->first();
    }
}
