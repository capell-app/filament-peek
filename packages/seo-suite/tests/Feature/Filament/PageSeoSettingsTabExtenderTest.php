<?php

declare(strict_types=1);

use Capell\Admin\Enums\PageTranslationSchemaHookEnum;
use Capell\Admin\Testing\Filament\ReadsRawSchemaComponents;
use Capell\Core\Models\Page;
use Capell\SeoSuite\Enums\RobotsDirectiveEnum;
use Capell\SeoSuite\Filament\Extenders\Page\PageSeoSettingsTabExtender;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

it('adds seo settings as a page editor tab', function (): void {
    $extender = resolve(PageSeoSettingsTabExtender::class);

    $tabs = $extender->extendTabs(Schema::make(), []);
    $seoTab = $tabs[0] ?? null;
    $section = $seoTab instanceof Tab ? (ReadsRawSchemaComponents::childComponents($seoTab)[0] ?? null) : null;
    $components = $section instanceof Section ? ReadsRawSchemaComponents::childComponents($section) : [];
    $robotsField = collect($components)->first(fn (mixed $component): bool => $component instanceof CheckboxList);
    $metaTagsField = collect($components)->first(fn (mixed $component): bool => $component instanceof Textarea);

    expect($tabs)->toHaveCount(1)
        ->and($seoTab)->toBeInstanceOf(Tab::class)
        ->and($section)->toBeInstanceOf(Section::class)
        ->and($robotsField)->toBeInstanceOf(CheckboxList::class)
        ->and($robotsField->getName())->toBe('robots')
        ->and($robotsField->getOptions())->toBe(
            collect(RobotsDirectiveEnum::cases())
                ->mapWithKeys(fn (RobotsDirectiveEnum $directive): array => [$directive->value => $directive->getLabel()])
                ->all(),
        )
        ->and($metaTagsField)->toBeInstanceOf(Textarea::class)
        ->and($metaTagsField->getName())->toBe('meta_tags');
});

it('leaves unrelated page schema extension points unchanged', function (): void {
    $extender = resolve(PageSeoSettingsTabExtender::class);
    $page = Page::factory()->create();
    $relationManagers = ['existing'];

    expect($extender->extendTranslationComponentsForHook(Schema::make(), PageTranslationSchemaHookEnum::AfterSearchMeta))
        ->toBe([])
        ->and($extender->extendRelationManagers($page, $relationManagers))->toBe($relationManagers)
        ->and($extender->extendSidebarComponents(Schema::make()))->toBe([]);
});
