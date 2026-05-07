<?php

declare(strict_types=1);

namespace Capell\Newsletter\Listeners;

use Capell\Newsletter\Actions\SubscribeFromFormSubmissionAction;
use Illuminate\Support\Facades\Schema;

class SubscribeFromFormSubmission
{
    public function handle(object $event): void
    {
        if (! Schema::hasTable('newsletter_form_mappings') || ! Schema::hasTable('newsletter_subscribers')) {
            return;
        }

        SubscribeFromFormSubmissionAction::run($event);
    }
}
