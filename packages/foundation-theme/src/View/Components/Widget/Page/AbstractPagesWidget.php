<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Widget\Page;

use Capell\FoundationTheme\View\Components\Widget\AbstractWidget;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

abstract class AbstractPagesWidget extends AbstractWidget
{
    public ?string $componentItem = null;

    public Collection $pages;

    protected static string $defaultView = 'capell-layout-builder::components.widget.asset.pages';

    public function render(array $data = []): View|string|Closure
    {
        if ($this->skipRender && config('capell-layout-builder.element.skip_render_empty', true) === true) {
            return '';
        }

        return parent::render([
            'pages' => $this->pages,
        ]);
    }
}
