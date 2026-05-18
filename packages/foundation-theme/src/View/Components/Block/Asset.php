<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Block;

class Asset extends AbstractBlock
{
    protected static string $defaultView = 'capell-foundation-theme::components.block.asset.index';

    protected function mountBlock(): void
    {
        if ($this->block->assets->isEmpty() && config('capell-layout-builder.block.skip_render_empty', true) === true) {
            $this->skipRender = true;
        }
    }
}
