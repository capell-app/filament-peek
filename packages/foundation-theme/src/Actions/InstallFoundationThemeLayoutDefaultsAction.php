<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\Actions;

use Capell\Core\Enums\ContainerWidthEnum;
use Capell\Core\Enums\LayoutEnum;
use Capell\Core\Models\Layout;
use Capell\Core\Support\Creator\LayoutCreator;
use Capell\LayoutBuilder\Actions\ApplyLayoutSidebarBlockContributionsAction;
use Capell\LayoutBuilder\Support\Creator\BlockCreator;
use Capell\LayoutBuilder\Support\LayoutModelRegistrar;
use Lorisleiva\Actions\Concerns\AsObject;

/**
 * @method static array{created: int, updated: int, skipped: int} run(bool $force = false)
 */
final class InstallFoundationThemeLayoutDefaultsAction
{
    use AsObject;

    /**
     * @return array{created: int, updated: int, skipped: int}
     */
    public function handle(bool $force = false): array
    {
        LayoutModelRegistrar::register();

        $layoutCreator = resolve(LayoutCreator::class);
        $layoutCreator->createHomeLayout();
        $layoutCreator->createDefaultLayout();

        $blockCreator = resolve(BlockCreator::class);
        $blockCreator->breadcrumbBlock();
        $blockCreator->childrenBlock();
        $blockCreator->latestPagesBlock();
        $blockCreator->pageContentBlock();
        $blockCreator->siblingsBlock();

        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($this->layoutDefaults() as $layoutKey => $containers) {
            $layout = $this->resolveLayout($layoutKey);
            $hadContainers = $layout->containers !== [];

            if ($hadContainers && ! $force) {
                $result['skipped']++;

                continue;
            }

            $layout->update([
                'containers' => $containers,
                'blocks' => $this->blockKeys($containers),
            ]);

            ApplyLayoutSidebarBlockContributionsAction::run($layout);

            $result[$hadContainers ? 'updated' : 'created']++;
        }

        return $result;
    }

    private function resolveLayout(string $layoutKey): Layout
    {
        return Layout::query()->where('key', $layoutKey)->firstOrFail();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function layoutDefaults(): array
    {
        return [
            LayoutEnum::Home->value => [
                'main' => $this->mainContainer([
                    ['block_key' => 'page-content'],
                ], 12),
            ],
            LayoutEnum::Default->value => [
                'main' => $this->mainContainer([
                    ['block_key' => 'breadcrumbs'],
                    ['block_key' => 'page-content'],
                    ['block_key' => 'children'],
                ]),
                'sidebar' => $this->sidebarContainer([
                    ['block_key' => 'siblings'],
                    ['block_key' => 'latest-pages'],
                ]),
            ],
        ];
    }

    /**
     * @param  array<int, array<string, string>>  $blocks
     * @return array<string, mixed>
     */
    private function sidebarContainer(array $blocks): array
    {
        return [
            'meta' => [
                'colspan' => 3,
                'override_columns' => 1,
                'container' => ContainerWidthEnum::Full,
                'tag' => 'aside',
                'padding' => ['md'],
                'html_class' => 'sidebar-sticky space-y-8',
            ],
            'blocks' => $blocks,
        ];
    }

    /**
     * @param  array<int, array<string, string>>  $blocks
     * @return array<string, mixed>
     */
    private function mainContainer(array $blocks, int $colspan = 9): array
    {
        return [
            'meta' => [
                'colspan' => $colspan,
            ],
            'blocks' => $blocks,
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $containers
     * @return array<int, string>
     */
    private function blockKeys(array $containers): array
    {
        return collect($containers)
            ->flatMap(fn (array $container): array => $container['blocks'] ?? [])
            ->unique('block_key')
            ->pluck('block_key')
            ->values()
            ->all();
    }
}
