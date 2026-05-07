<?php

declare(strict_types=1);

namespace Capell\Newsletter\Console\Commands;

use Capell\Newsletter\Actions\RequeueDueProviderSyncAttemptsAction;
use Illuminate\Console\Command;

class RequeueDueProviderSyncAttemptsCommand extends Command
{
    protected $signature = 'newsletter:sync-retry-due {--limit= : Maximum number of due attempts to requeue}';

    protected $description = 'Requeue newsletter provider sync attempts whose retry time has passed.';

    public function handle(): int
    {
        $limit = $this->option('limit');
        $count = RequeueDueProviderSyncAttemptsAction::run(is_numeric($limit) ? (int) $limit : null);

        $this->components->info(sprintf('Requeued %d newsletter provider sync attempt%s.', $count, $count === 1 ? '' : 's'));

        return self::SUCCESS;
    }
}
