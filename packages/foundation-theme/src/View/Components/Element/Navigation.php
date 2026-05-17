<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Element;

use Capell\FoundationTheme\Support\NavigationAvailability;
use Capell\Frontend\Facades\Frontend;
use Capell\Navigation\Actions\BuildNavigationRenderModelAction;
use Capell\Navigation\Data\NavigationRenderContextData;
use Capell\Navigation\Data\NavigationRenderData;
use Capell\Navigation\Models;
use Capell\Navigation\Support\Loader\NavigationLoader;
use Illuminate\Support\Collection;

class Navigation extends AbstractElement
{
    public ?Collection $items = null;

    public ?Models\Navigation $menu = null;

    public ?NavigationRenderData $navigationRenderData = null;

    protected static string $defaultView = 'capell-foundation-theme::components.element.navigation.index';

    protected function mountElement(): void
    {
        if (! NavigationAvailability::check()) {
            $this->skipRender = true;

            return;
        }

        $menu = $this->getElementMenu();

        if (! $menu instanceof Models\Navigation) {
            if (config('capell-layout-builder.element.skip_render_empty', true) === true) {
                $this->skipRender = true;
            }

            return;
        }

        $this->menu = $menu;

        $this->navigationRenderData = BuildNavigationRenderModelAction::run(new NavigationRenderContextData(
            navigation: $this->menu,
            page: Frontend::page(),
            site: Frontend::site(),
            language: Frontend::language(),
            siteDomain: Frontend::site()->siteDomain,
        ));

        $this->items = $this->navigationRenderData->items;

        if ($this->items->isEmpty()) {
            if (config('capell-layout-builder.element.skip_render_empty', true) === true) {
                $this->skipRender = true;
            }

            return;
        }
    }

    private function getElementMenu(): ?Models\Navigation
    {
        if (isset($this->element->meta['navigation_id']) && is_numeric($this->element->meta['navigation_id'])) {
            return NavigationLoader::getNavigationById($this->element->meta['navigation_id']);
        }

        if (! isset($this->element->meta['navigation']) || ! is_string($this->element->meta['navigation'])) {
            return null;
        }

        return NavigationLoader::getNavigation(
            $this->element->meta['navigation'],
            Frontend::site(),
            Frontend::language(),
        );
    }
}
