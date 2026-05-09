<?php

declare(strict_types=1);

namespace Capell\PublicActions\Jobs;

use Capell\PublicActions\Actions\DispatchPublicActionDestinationAction;
use Capell\PublicActions\Models\PublicActionDestination;
use Capell\PublicActions\Models\PublicActionSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class DispatchPublicActionDestinationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public PublicActionDestination $destination,
        public PublicActionSubmission $submission,
    ) {
        $this->onQueue(config('capell-public-actions.queue', 'default'));
    }

    public function handle(DispatchPublicActionDestinationAction $dispatchDestination): void
    {
        $dispatchDestination->handle($this->destination, $this->submission);
    }
}
