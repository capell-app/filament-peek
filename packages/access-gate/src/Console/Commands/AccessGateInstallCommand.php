<?php

declare(strict_types=1);

namespace Capell\AccessGate\Console\Commands;

use Illuminate\Console\Command;

final class AccessGateInstallCommand extends Command
{
    protected $signature = 'capell:access-gate-install';

    protected $description = 'Publish Access Gate configuration, migrations, views, and translations.';

    public function handle(): int
    {
        foreach ($this->publishTags() as $tag) {
            $this->callSilent('vendor:publish', [
                '--tag' => $tag,
                '--force' => false,
            ]);
        }

        $this->info(__('capell-access-gate::install.published'));

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function publishTags(): array
    {
        return [
            'capell-access-gate-config',
            'capell-access-gate-migrations',
            'capell-access-gate-views',
            'capell-access-gate-translations',
        ];
    }
}
