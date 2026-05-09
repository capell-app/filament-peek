<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Jobs;

use Capell\EmailStudio\Actions\DeliverEmailMessageAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $emailMessageId,
    ) {}

    public function handle(): void
    {
        DeliverEmailMessageAction::run($this->emailMessageId);
    }
}
