<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Widget;

use Capell\Frontend\Facades\Frontend;
use Capell\LayoutBuilder\Models\Element;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use stdClass;
use Throwable;

abstract class AbstractWidget extends Component
{
    protected static string $defaultView = 'capell-layout-builder::components.widget.default';

    protected bool $skipRender = false;

    public function __construct(
        public array $container,
        public string $containerKey,
        public int $widgetIndex,
        public stdClass $loop,
        public Element $widget,
        public array $widgetData = [],
        public mixed $pageSlot = null,
    ) {
        $this->mountWidget();
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
            'widget' => $this->widget,
            'widgetData' => $this->widgetData,
            'widgetIndex' => $this->widgetIndex,
            'pageSlot' => $this->pageSlot,
            ...$data,
        ];

        if ($this->skipRender && config('capell-layout-builder.element.skip_render_empty', true) === true) {
            return '';
        }

        if (isset($this->widget->meta['view_file']) && $this->widget->meta['view_file'] !== '') {
            $component = $this->widget->meta['view_file'];
        } elseif (isset($this->widget->type->meta['view_file']) && $this->widget->type->meta['view_file'] !== '') {
            $component = $this->widget->type->meta['view_file'];
        } else {
            $component = static::$defaultView;
        }

        $data['component_item'] = $this->getComponentItem();

        return view($component, $data);
    }

    protected function getComponentItem(): ?string
    {
        return $this->widget->meta['component_item'] ?? $this->widget->type->meta['component_item'] ?? null;
    }

    protected function mountWidget(): void {}

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
