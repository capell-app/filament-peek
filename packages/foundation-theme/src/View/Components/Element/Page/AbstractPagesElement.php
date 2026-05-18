<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Element\Page;

use Capell\FoundationTheme\View\Components\Element\AbstractElement;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Override;

abstract class AbstractPagesElement extends AbstractElement
{
    public ?string $componentItem = null;

    public ?Collection $pages = null;

    protected static string $defaultView = 'capell-foundation-theme::components.element.asset.pages';

    #[Override]
    public function render(array $data = []): View|string|Closure
    {
        if ($this->skipRender && config('capell-layout-builder.element.skip_render_empty', true) === true) {
            return '';
        }

        return parent::render([
            ...$data,
            'pages' => $this->pages ?? collect(),
        ]);
    }
}
