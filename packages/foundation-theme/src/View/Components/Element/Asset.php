<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Element;

class Asset extends AbstractElement
{
    protected static string $defaultView = 'capell-foundation-theme::components.element.asset.index';

    protected function mountElement(): void
    {
        if ($this->element->assets->isEmpty() && config('capell-layout-builder.element.skip_render_empty', true) === true) {
            $this->skipRender = true;
        }
    }
}
