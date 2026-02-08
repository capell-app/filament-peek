<?php

declare(strict_types=1);

use Capell\Blog\Actions\InstallPackageAction;
use Capell\Core\Console\Commands\PublishMigrationsCommand;
use Capell\Core\Support\Dataset\DatasetPublisher;
use Capell\Core\Support\Migration\MigrationFileManagerInterface;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Migrations\Migrator;

beforeEach(function (): void {
    $this->fakeFileManager = new class implements MigrationFileManagerInterface
    {
        public array $calls = [];

        public function fileExists(string $path): bool
        {
            $this->calls[] = ['fileExists', $path];

            return false;
        }

        public function isDir(string $path): bool
        {
            $this->calls[] = ['isDir', $path];

            return true;
        }

        public function makeDir(string $path): void
        {
            $this->calls[] = ['makeDir', $path];
        }

        public function copy(string $from, string $to): void
        {
            $this->calls[] = ['copy', $from, $to];
        }
    };

    $fakeDatasetPublisher = \Mockery::mock(DatasetPublisher::class);

    // Ensure calls to publish migrations are no-ops and counted (called twice in Blog install)
    $this->instance(
        PublishMigrationsCommand::class,
        \Mockery::mock(new PublishMigrationsCommand($fakeDatasetPublisher, $this->fakeFileManager))
            ->makePartial()
            ->shouldReceive('run')->twice()->andReturn(0)->getMock(),
    );

    // Ensure migrate command is a no-op
    $fakeMigrator = \Mockery::mock(Migrator::class);
    $fakeDispatcher = \Mockery::mock(Dispatcher::class);
    $this->instance(
        MigrateCommand::class,
        \Mockery::mock(new MigrateCommand($fakeMigrator, $fakeDispatcher))
            ->makePartial()
            ->shouldReceive('run')->once()->andReturn(0)->getMock(),
    );

    // If Filament AssetsCommand is available, stub it as a no-op
    if (class_exists('Filament\\Commands\\AssetsCommand')) {
        $this->instance(
            'Filament\\Commands\\AssetsCommand',
            \Mockery::mock('Filament\\Commands\\AssetsCommand', [])->makePartial()
                ->shouldReceive('run')->once()->andReturn(0)->getMock(),
        );
    }

    app()->instance(MigrationFileManagerInterface::class, $this->fakeFileManager);
});

afterEach(function (): void {
    \Mockery::close();
});

it('runs blog install command successfully without publishing files', function (): void {
    $mock = InstallPackageAction::mock();
    $mock->shouldReceive('handle')->once();
    app()->instance(InstallPackageAction::class, $mock);

    $this->artisan('capell-blog:install')
        ->expectsOutput('Installing Capell Blog Package...')
        ->doesntExpectOutput('Publishing migrations')
        ->doesntExpectOutput('Migrating')
        ->doesntExpectOutput('Building assets')
        ->expectsOutput('Capell Blog installation complete.')
        ->assertExitCode(Command::SUCCESS);

    // Assert no migration files were actually published
    expect($this->fakeFileManager->calls)
        ->not()->toContain(fn (array $call): bool => $call[0] === 'copy')
        ->toBeArray();

    // Assert no directory/file operations were attempted by the publish command internals
    expect(collect($this->fakeFileManager->calls)->contains(
        fn (array $call): bool => in_array($call[0], ['fileExists', 'isDir', 'makeDir'], true),
    ))->toBeFalse();
});
