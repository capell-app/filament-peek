<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Element;

use Capell\FoundationTheme\Support\View\FoundationThemeViewName;
use Capell\Frontend\Facades\Frontend;
use Capell\LayoutBuilder\Models\Element;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use stdClass;
use Throwable;

abstract class AbstractElement extends Component
{
    protected static string $defaultView = 'capell-foundation-theme::components.element.default';

    protected bool $skipRender = false;

    public function __construct(
        public array $container,
        public string $containerKey,
        public int $elementIndex,
        public stdClass $loop,
        public Element $element,
        public array $elementData = [],
        public mixed $pageSlot = null,
    ) {
        $this->mountElement();
    }

    public function render(array $data = []): View|string|Closure
    {
        $data = [
            'container' => $this->container,
            'containerKey' => $this->containerKey,
            'loop' => $this->loop,
            'language' => $this->frontendContextValue('language'),
            'layout' => $this->frontendContextValue('layout'),
            'pageRecord' => $this->frontendContextValue('page'),
            'site' => $this->frontendContextValue('site'),
            'theme' => $this->frontendContextValue('theme'),
            'urlParams' => $this->frontendParams(),
            'element' => $this->element,
            'elementData' => $this->elementData,
            'elementIndex' => $this->elementIndex,
            'pageSlot' => $this->pageSlot,
            ...$data,
        ];

        if ($this->skipRender && config('capell-layout-builder.element.skip_render_empty', true) === true) {
            return '';
        }

        $data['component_item'] = $this->getComponentItem();

        return view(FoundationThemeViewName::canonical($this->getViewFile()), $data);
    }

    protected function getComponentItem(): ?string
    {
        return $this->element->getComponentItem();
    }

    protected function getViewFile(): string
    {
        return $this->element->getViewFile() ?? static::$defaultView;
    }

    protected function mountElement(): void {}

    private function frontendContextValue(string $method): mixed
    {
        try {
            return match ($method) {
                'language' => Frontend::language(),
                'layout' => Frontend::layout(),
                'page' => Frontend::page(),
                'site' => Frontend::site(),
                'theme' => Frontend::theme(),
                default => null,
            };
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function frontendParams(): array
    {
        try {
            return Frontend::params();
        } catch (Throwable) {
            return [];
        }
    }
}
