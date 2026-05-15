<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Element;

use Capell\LayoutBuilder\Models\Element;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use stdClass;

abstract class AbstractElement extends Component
{
    protected static string $defaultView = 'capell-layout-builder::components.element.default';

    protected bool $skipRender = false;

    public function __construct(
        public array $container,
        public string $containerKey,
        public int $elementIndex,
        public stdClass $loop,
        public Element $element,
        public array $elementData = [],
    ) {
        $this->mountElement();
    }

    public function render(array $data = []): View|string|Closure
    {
        if ($this->skipRender && config('capell-layout-builder.element.skip_render_empty', true) === true) {
            return '';
        }

        $data['component_item'] = $this->getComponentItem();

        return view($this->componentView(), $data);
    }

    protected function getComponentItem(): ?string
    {
        return $this->element->meta['component_item'] ?? $this->element->type->meta['component_item'] ?? null;
    }

    protected function mountElement(): void {}

    private function componentView(): string
    {
        if (isset($this->element->meta['view_file']) && $this->element->meta['view_file'] !== '') {
            return $this->element->meta['view_file'];
        }

        if (isset($this->element->type->meta['view_file']) && $this->element->type->meta['view_file'] !== '') {
            return $this->element->type->meta['view_file'];
        }

        return static::$defaultView;
    }
}
