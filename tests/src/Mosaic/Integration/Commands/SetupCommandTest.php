<?php

declare(strict_types=1);

namespace Capell\Tests\Mosaic\Integration\Commands;

use Capell\Core\Models\Layout;
use Capell\Mosaic\Actions\AddHeroWidgetToLayoutAction;
use Capell\Mosaic\Actions\InstallPackageAction;
use Illuminate\Console\Command;

use function Pest\Laravel\artisan;

it('runs mosaic setup command successfully', function (): void {
    InstallPackageAction::shouldRun()->once();

    artisan('capell:mosaic-setup')
        ->expectsOutput('Capell Mosaic setup successfully.')
        ->doesntExpectOutput('Running hero setup...')
        ->assertExitCode(Command::SUCCESS);
});

it('runs mosaic setup and invokes hero setup when --include-hero is passed', function (): void {
    InstallPackageAction::shouldRun()->once();
    AddHeroWidgetToLayoutAction::shouldRun();

    Layout::factory()->default()->create();

    artisan('capell:mosaic-setup', ['--include-hero' => true])
        ->expectsOutput('Capell Mosaic setup successfully.')
        ->expectsOutput('Running hero setup...')
        ->expectsOutput('Capell Hero setup successfully.')
        ->assertExitCode(Command::SUCCESS);
});

it('runs hero setup command directly', function (): void {
    AddHeroWidgetToLayoutAction::shouldRun()->once();

    Layout::factory()->default()->create();

    artisan('capell:hero-setup')
        ->expectsOutput('Capell Hero setup successfully.')
        ->assertExitCode(Command::SUCCESS);
});
