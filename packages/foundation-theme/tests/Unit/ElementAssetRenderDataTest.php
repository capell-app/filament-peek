<?php

declare(strict_types=1);

use Capell\Core\Database\Factories\MediaFactory;
use Capell\Core\Enums\MediaCollectionEnum;
use Capell\Core\Models\Page;
use Capell\Core\Models\Translation;
use Capell\FoundationTheme\Actions\BuildElementAssetRenderDataAction;
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

    $widgetAsset = new ElementAsset(['asset_type' => Page::class]);
    $widgetAsset->setRelation('asset', $asset);

    DB::enableQueryLog();

    $renderData = BuildElementAssetRenderDataAction::run($widgetAsset);

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
