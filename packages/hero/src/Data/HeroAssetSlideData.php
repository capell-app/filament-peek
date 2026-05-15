<?php

declare(strict_types=1);

namespace Capell\Hero\Data;

use Capell\Core\Enums\MediaCollectionEnum;
use Capell\Core\Models\Media;
use Capell\Core\Models\Page;
use Capell\LayoutBuilder\Models\Element;
use Capell\LayoutBuilder\Models\ElementAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final readonly class HeroAssetSlideData
{
    /**
     * @param  Collection<int, Media>|null  $images
     */
    public function __construct(
        public Model $asset,
        public string $color,
        public ?Page $linkedPage,
        public ?string $url,
        public ?Media $backgroundImage,
        public ?Collection $images,
    ) {}

    public static function fromElementAsset(ElementAsset $elementAsset, Element $element, string $fallbackColor): self
    {
        $asset = $elementAsset->asset;
        $color = $asset->getMeta('color', $fallbackColor);
        $linkedPage = $asset instanceof Page ? $asset : $asset->linkedPage;
        $backgroundImage = self::resolveBackgroundImage($elementAsset);
        $images = self::resolveImages($elementAsset);

        return new self(
            asset: $asset,
            color: is_string($color) && $color !== '' ? $color : $fallbackColor,
            linkedPage: $linkedPage instanceof Page ? $linkedPage : null,
            url: $linkedPage instanceof Page ? $linkedPage->pageUrl?->full_url : null,
            backgroundImage: $backgroundImage,
            images: $images,
        );
    }

    private static function resolveBackgroundImage(ElementAsset $elementAsset): ?Media
    {
        if ($elementAsset->asset instanceof Media) {
            return $elementAsset->asset;
        }

        $collection = MediaCollectionEnum::BackgroundImage->value;

        $media = $elementAsset->media?->firstWhere('collection_name', $collection)
            ?? $elementAsset->asset->media?->firstWhere('collection_name', $collection);

        return $media instanceof Media ? $media : null;
    }

    /**
     * @return Collection<int, Media>|null
     */
    private static function resolveImages(ElementAsset $elementAsset): ?Collection
    {
        if ($elementAsset->asset instanceof Media) {
            return null;
        }

        $collection = MediaCollectionEnum::Image->value;

        $images = $elementAsset->media?->where('collection_name', $collection);

        if (! $images?->isNotEmpty()) {
            $images = $elementAsset->asset->media?->where('collection_name', $collection);
        }

        return $images?->isNotEmpty() ? $images->values() : null;
    }
}
