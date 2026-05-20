<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Console\Commands;

use Capell\Core\Support\Migration\MigrationFilesystemInterface;
use Capell\SeoSuite\Actions\SeedDefaultAiCrawlerRulesAction;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'capell:seo-suite-install';

    public function __construct(private readonly MigrationFilesystemInterface $fileManager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $migrations = __DIR__ . '/../../../database/migrations';
        if (! $this->fileManager->isDir($migrations)) {
            $this->error('Migrations directory does not exist.');

            return Command::FAILURE;
        }

        $this->call('capell:publish-migrations', [
            '--items' => [
                '2026_05_10_190870_01_create_ai_creator_contexts_table',
                '2026_05_10_190870_02_create_ai_generation_histories_table',
                '2026_05_10_190870_03_create_ai_creator_sessions_table',
                '2026_05_10_190870_04_create_ai_discovery_crawler_rules_table',
                '2026_05_10_190870_05_create_ai_discovery_page_profiles_table',
                '2026_05_10_190870_06_create_ai_discovery_site_profiles_table',
                '2026_05_10_190870_07_create_ai_discovery_snapshots_table',
                '2026_05_10_190870_08_create_broken_links_table',
                '2026_05_10_190870_09_create_page_seo_snapshots_table',
                '2026_05_10_190870_10_create_search_console_url_metrics_table',
            ],
            '--path' => $migrations,
        ]);

        $settings = __DIR__ . '/../../../database/settings';
        if (! $this->fileManager->isDir($settings)) {
            $this->error('Settings directory does not exist.');

            return Command::FAILURE;
        }

        $this->call('capell:publish-migrations', [
            '--type' => 'settings',
            '--items' => [
                '2026_05_10_190871_01_create_ai-orchestrator_settings',
                '2026_05_10_190871_03_create_seo_suite_settings',
            ],
            '--path' => $settings,
        ]);

        $this->info('Migrations published successfully.');

        $this->call('migrate');
        SeedDefaultAiCrawlerRulesAction::run();

        $this->newLine();
        $this->info('Capell SEO Suite installed successfully.');

        return Command::SUCCESS;
    }
}
