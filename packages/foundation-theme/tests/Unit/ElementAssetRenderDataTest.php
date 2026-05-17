<?php

declare(strict_types=1);

use Capell\Core\Database\Factories\MediaFactory;
use Capell\Core\Enums\ContentStructure;
use Capell\Core\Enums\MediaCollectionEnum;
use Capell\Core\Models\Page;
use Capell\Core\Models\Translation;
use Capell\FoundationTheme\Actions\BuildElementAssetRenderDataAction;
use Capell\FoundationTheme\Actions\BuildHeroRailItemsRenderDataAction;
use Capell\FoundationTheme\Actions\BuildPageContentRenderDataAction;
use Capell\LayoutBuilder\Models\Element;
use Capell\LayoutBuilder\Models\ElementAsset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

it('builds element asset render data from loaded relations only', function (): void {
    $media = MediaFactory::new()->make([
        'collection_name' => MediaCollectionEnum::Image->value,
    ]);
    $translation = new Translation;
    $translation->setRawAttributes([
        'title' => 'North Star',
        'content' => '<p>Guidance copy.</p>',
        'label' => 'North Star label',
    ]);
    $linkedPage = new Page;
    $asset = new class extends Model
    {
        use HasFactory;

        protected $guarded = [];

        public function getMeta(string $key, mixed $default = null): mixed
        {
            return data_get($this->getAttribute('meta'), $key, $default);
        }
    };

    $asset->setRawAttributes([
        'meta' => [
            'icon' => 'heroicon-o-star',
            'position' => 'right',
            'social' => ['website' => 'https://example.test'],
            'tags' => ['Featured'],
        ],
    ]);
    $asset->setRelation('media', new Collection([$media]));
    $asset->setRelation('translation', $translation);
    $asset->setRelation('linkedPage', $linkedPage);

    $elementAsset = new ElementAsset(['asset_type' => Page::class]);
    $elementAsset->setRelation('asset', $asset);

    DB::enableQueryLog();

    $renderData = BuildElementAssetRenderDataAction::run($elementAsset);

    expect($renderData->image)->toBe($media)
        ->and($renderData->linkedPage)->toBe($linkedPage)
        ->and($renderData->title)->toBe('North Star')
        ->and($renderData->alt)->toBe('North Star')
        ->and($renderData->content)->toBe('<p>Guidance copy.</p>')
        ->and($renderData->icon)->toBe('heroicon-o-star')
        ->and($renderData->position)->toBe('right')
        ->and($renderData->social)->toBe(['website' => 'https://example.test'])
        ->and($renderData->tags)->toBe(['Featured'])
        ->and(DB::getQueryLog())->toBe([]);

    DB::disableQueryLog();
});

it('builds page content render data from loaded relations only', function (): void {
    $page = Page::factory()->make();
    $translation = new Translation;
    $translation->setRawAttributes([
        'title' => 'Loaded page title',
        'content' => '<p>Loaded page content.</p>',
    ]);

    $page->setRelation('translation', $translation);

    DB::enableQueryLog();

    $renderData = BuildPageContentRenderDataAction::run($page, ['content'], true);

    expect($renderData->title)->toBe('Loaded page title')
        ->and($renderData->content)->toBe('<p>Loaded page content.</p>')
        ->and($renderData->contentStructure)->toBe(ContentStructure::Html)
        ->and($renderData->hasContent)->toBeTrue()
        ->and($renderData->hasTitle)->toBeFalse()
        ->and(DB::getQueryLog())->toBe([]);

    DB::disableQueryLog();
});

it('builds hero rail items from loaded explicit hero assets only', function (): void {
    $element = new Element;
    $elementAsset = heroRailElementAsset('element-card', 'Element card');
    $page = new Page;
    $pageHeroAsset = heroRailElementAsset('hero-card', 'Hero card');
    $pageGenericAsset = heroRailElementAsset('card', 'Generic card');

    $element->setRelation('assets', new Collection([$elementAsset]));
    $page->setRelation('assets', new Collection([$pageHeroAsset, $pageGenericAsset]));

    DB::enableQueryLog();

    $pageItems = BuildHeroRailItemsRenderDataAction::run($element, $page, 'page');
    $mixedItems = BuildHeroRailItemsRenderDataAction::run($element, $page, 'mixed');
    $elementItems = BuildHeroRailItemsRenderDataAction::run($element, $page, 'element');

    expect($pageItems)->toHaveCount(1)
        ->and($pageItems[0]->caption)->toBe('Hero card')
        ->and($mixedItems)->toHaveCount(2)
        ->and($mixedItems[0]->caption)->toBe('Hero card')
        ->and($mixedItems[1]->caption)->toBe('Element card')
        ->and($elementItems)->toHaveCount(1)
        ->and($elementItems[0]->caption)->toBe('Element card')
        ->and(DB::getQueryLog())->toBe([]);

    DB::disableQueryLog();
});

function heroRailElementAsset(string $role, string $caption): ElementAsset
{
    $asset = new class extends Model
    {
        use HasFactory;

        protected $guarded = [];

        public function getMeta(string $key, mixed $default = null): mixed
        {
            return data_get($this->getAttribute('meta'), $key, $default);
        }
    };
    $asset->setRawAttributes([
        'meta' => [
            'caption' => $caption,
            'role' => $role,
        ],
    ]);

    $elementAsset = new ElementAsset(['asset_type' => Page::class]);
    $elementAsset->setRelation('asset', $asset);

    return $elementAsset;
}
