<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\Actions;

use Capell\Core\Enums\ContainerWidthEnum;
use Capell\Core\Enums\LayoutEnum;
use Capell\Core\Models\Layout;
use Capell\Core\Support\Creator\LayoutCreator;
use Capell\LayoutBuilder\Actions\ApplyLayoutSidebarElementContributionsAction;
use Capell\LayoutBuilder\Support\Creator\ElementCreator;
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

        $elementCreator = resolve(ElementCreator::class);
        $elementCreator->breadcrumbElement();
        $elementCreator->childrenElement();
        $elementCreator->latestPagesElement();
        $elementCreator->pageContentElement();
        $elementCreator->siblingsElement();

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
                'elements' => $this->elementKeys($containers),
            ]);

            ApplyLayoutSidebarElementContributionsAction::run($layout);

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
                    ['element_key' => 'page-content'],
                ], 12),
            ],
            LayoutEnum::Default->value => [
                'main' => $this->mainContainer([
                    ['element_key' => 'breadcrumbs'],
                    ['element_key' => 'page-content'],
                    ['element_key' => 'children'],
                ]),
                'sidebar' => $this->sidebarContainer([
                    ['element_key' => 'siblings'],
                    ['element_key' => 'latest-pages'],
                ]),
            ],
        ];
    }

    /**
     * @param  array<int, array<string, string>>  $elements
     * @return array<string, mixed>
     */
    private function sidebarContainer(array $elements): array
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
            'elements' => $elements,
        ];
    }

    /**
     * @param  array<int, array<string, string>>  $elements
     * @return array<string, mixed>
     */
    private function mainContainer(array $elements, int $colspan = 9): array
    {
        return [
            'meta' => [
                'colspan' => $colspan,
            ],
            'elements' => $elements,
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $containers
     * @return array<int, string>
     */
    private function elementKeys(array $containers): array
    {
        return collect($containers)
            ->flatMap(fn (array $container): array => $container['elements'] ?? [])
            ->unique('element_key')
            ->pluck('element_key')
            ->values()
            ->all();
    }
}
