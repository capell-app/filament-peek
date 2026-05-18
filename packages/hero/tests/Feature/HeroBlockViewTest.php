<?php

declare(strict_types=1);

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Core\Models\Theme;
use Capell\Frontend\Support\State\FrontendState;
use Capell\LayoutBuilder\Enums\BlockComponentEnum;
use Capell\LayoutBuilder\Models\Block;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

it('renders page translation hero content while ignoring nested page variables', function (): void {
    $language = Language::factory()->english()->create();
    $theme = Theme::factory()->defaultMeta()->create();
    $site = Site::factory()
        ->language($language)
        ->theme($theme)
        ->withTranslations($language, ['title' => 'Capell'])
        ->create();

    $page = Page::factory()
        ->site($site)
        ->withTranslations($language, [
            'title' => 'Platform Architecture',
            'content' => '<p>Body content.</p>',
            'meta' => [
                'hero' => '<p>Build :title for :site without touching :page.</p>',
                'hero_title' => ':title',
                'slug' => 'platform-architecture',
            ],
        ])
        ->create();

    $page->load('translation');

    $site->load('translation');

    $block = Block::factory()->create([
        'key' => 'hero',
        'meta' => [
            'component' => BlockComponentEnum::Hero->value,
            'color' => 'light',
            'content_width' => 'balanced',
        ],
    ]);
    $block->setRelation('assets', new EloquentCollection);

    resolve(FrontendState::class)
        ->withLanguage($language)
        ->withSite($site)
        ->withTheme($theme)
        ->withPage($page);

    $view = $this->view('capell-hero::components.block.hero', [
        'containerKey' => 'main',
        'containerIndex' => 0,
        'block' => $block,
        'blockIndex' => 0,
        'loop' => (object) ['first' => true, 'last' => true],
    ]);

    $view
        ->assertSee('Platform Architecture')
        ->assertSee('Build Platform Architecture for Capell without touching :page.', false);
});
