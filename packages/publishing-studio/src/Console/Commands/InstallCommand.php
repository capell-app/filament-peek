<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Console\Commands;

use Capell\PublishingStudio\Actions\InstallWorkspaceRolesAction;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /** @var string */
    protected $description = 'Install publishing-studio package';

    /** @var string */
    protected $signature = 'capell:publishing-studio-install';

    public function handle(): int
    {
        if (! $this->publishMigrations()) {
            return self::FAILURE;
        }

        $this->call('migrate');

        InstallWorkspaceRolesAction::run();

        $this->newLine();
        $this->info('Capell PublishingStudio installed successfully.');

        return self::SUCCESS;
    }

    private function publishMigrations(): bool
    {
        $migrations = [
            __DIR__ . '/../../../database/migrations/2026_05_10_190866_02_create_publishing-studio_table.php',
            __DIR__ . '/../../../database/migrations/2026_05_10_190866_03_create_versions_table.php',
            __DIR__ . '/../../../database/migrations/2026_05_10_190866_04_create_workspace_approvals_table.php',
            __DIR__ . '/../../../database/migrations/2026_05_10_190866_06_create_workspace_review_assignments_table.php',
            __DIR__ . '/../../../database/migrations/2026_05_10_190866_05_create_workspace_field_comments_table.php',
            __DIR__ . '/../../../database/migrations/2026_05_10_190866_01_create_preview_links_table.php',
            __DIR__ . '/../../../database/migrations/2026_05_10_190866_07_seed_bootstrap_workspace_version.php',
            __DIR__ . '/../../../database/migrations/2026_05_10_190866_08_z_add_workspace_columns_to_core_tables.php',
            __DIR__ . '/../../../database/migrations/2026_05_10_190866_10_z_add_workspace_id_to_import_sessions_table.php',
            __DIR__ . '/../../../database/migrations/2026_05_10_190866_09_z_add_workspace_id_to_external_tables.php',
        ];

        return $this->call('capell:publish-migrations', ['--items' => $migrations]) === self::SUCCESS;
    }
}
