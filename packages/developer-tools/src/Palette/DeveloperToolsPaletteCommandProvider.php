<?php

declare(strict_types=1);

namespace Capell\DeveloperTools\Palette;

use Capell\Admin\Contracts\Palette\PaletteCommandProvider;
use Capell\Admin\Data\PaletteCommandData;
use Capell\Admin\Enums\PaletteCommandType;
use Capell\DeveloperTools\Filament\Pages\DeveloperToolsPage;
use Capell\DeveloperTools\Filament\Pages\QueueHealthPage;
use Capell\DeveloperTools\Filament\Pages\SystemHealthPage;
use Throwable;

final class DeveloperToolsPaletteCommandProvider implements PaletteCommandProvider
{
    /**
     * @return array<string, PaletteCommandData>
     */
    public function paletteCommands(): array
    {
        $commands = array_filter([
            $this->navigationCommand(
                id: 'developer-tools.open',
                label: 'Open developer tools',
                description: 'Open the Capell developer tools workspace.',
                page: DeveloperToolsPage::class,
                keywords: ['developer', 'tools', 'registry', 'makers'],
                sort: 10,
            ),
            $this->navigationCommand(
                id: 'developer-tools.system-health',
                label: 'Open system health',
                description: 'Review setup, cache, package, registry, and migration health.',
                page: SystemHealthPage::class,
                keywords: ['health', 'cache', 'package', 'registry', 'migration'],
                sort: 11,
            ),
            $this->navigationCommand(
                id: 'developer-tools.queue-health',
                label: 'View failed jobs',
                description: 'Open the queue health report.',
                page: QueueHealthPage::class,
                keywords: ['queue', 'failed', 'jobs'],
                sort: 12,
            ),
        ]);

        return collect($commands)
            ->mapWithKeys(fn (PaletteCommandData $command): array => [$command->id => $command])
            ->all();
    }

    /**
     * @param  class-string  $page
     * @param  array<int, string>  $keywords
     */
    private function navigationCommand(
        string $id,
        string $label,
        string $description,
        string $page,
        array $keywords,
        int $sort,
    ): ?PaletteCommandData {
        try {
            return new PaletteCommandData(
                id: $id,
                label: $label,
                description: $description,
                url: $page::getUrl(panel: 'admin'),
                type: PaletteCommandType::Navigation,
                group: 'Developer tools',
                sort: $sort,
                keywords: [$page, ...$keywords],
            );
        } catch (Throwable) {
            return null;
        }
    }
}
