<?php

declare(strict_types=1);

it('renders the block section component used by portfolio widgets', function (): void {
    $asset = new class
    {
        public object $translation;

        /** @var array<string, mixed> */
        public array $meta = [];

        public function __construct()
        {
            $this->translation = (object) [
                'content' => '<p>Reusable content block.</p>',
                'title' => 'Strategy',
            ];
        }

        public function getMeta(string $key, mixed $default = null): mixed
        {
            return [
                'color' => 'primary',
                'icon' => null,
            ][$key] ?? $default;
        }
    };

    $this->blade(
        '<x-capell-layout-builder::section.block :asset="$asset" :loop="$loop" :meta="$asset->meta" color="primary" title="Strategy" summary="Reusable content block." :class="$class" />',
        [
            'asset' => $asset,
            'class' => ['widget-block-item'],
            'loop' => (object) ['index' => 0],
        ],
    )
        ->assertSee('Strategy')
        ->assertSee('Reusable content block.')
        ->assertSee('widget-block-item')
        ->assertSee('bg-primary');
});
