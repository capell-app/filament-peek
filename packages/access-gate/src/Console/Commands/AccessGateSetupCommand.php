<?php

declare(strict_types=1);

namespace Capell\AccessGate\Console\Commands;

use Capell\AccessGate\Actions\SetupDefaultAccessAreaAction;
use Illuminate\Console\Command;

final class AccessGateSetupCommand extends Command
{
    protected $signature = 'capell:access-gate-setup';

    protected $description = 'Create or update the configured default Access Gate area.';

    public function handle(SetupDefaultAccessAreaAction $setupDefaultArea): int
    {
        $area = $setupDefaultArea->handle();

        $this->info(__('capell-access-gate::install.default_area_ready', ['key' => $area->key]));

        return self::SUCCESS;
    }
}
