<?php

declare(strict_types=1);

it('renders the team member section component used by portfolio widgets', function (): void {
    $asset = new class
    {
        public object $translation;

        public function __construct()
        {
            $this->translation = (object) [
                'content' => '<p>Leads the product team.</p>',
                'title' => 'Alice Johnson',
            ];
        }

        public function getMeta(string $key, mixed $default = null): mixed
        {
            return [
                'position' => 'Product Lead',
            ][$key] ?? $default;
        }
    };

    $this->blade(
        '<x-capell-layout-builder::section.team-member :asset="$asset" :loop="$loop" :class="$class" />',
        [
            'asset' => $asset,
            'class' => ['widget-block-item'],
            'loop' => (object) ['index' => 0],
        ],
    )
        ->assertSee('Alice Johnson')
        ->assertSee('Product Lead')
        ->assertSee('Leads the product team.')
        ->assertSee('AJ')
        ->assertSee('widget-block-item');
});
