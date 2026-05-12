<?php

declare(strict_types=1);

namespace Capell\Hero\Data;

use Capell\Core\Enums\MediaCollectionEnum;
use Capell\Core\Models\Media;
use Capell\Core\Models\Page;
use Capell\Core\Models\Widget;
use Capell\Core\Models\WidgetAsset;
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

    public static function fromWidgetAsset(WidgetAsset $widgetAsset, Widget $widget, string $fallbackColor): self
    {
        $asset = $widgetAsset->asset;
        $color = $asset->getMeta('color', $fallbackColor);
        $linkedPage = $asset instanceof Page ? $asset : $asset->linkedPage;
        $backgroundImage = self::resolveBackgroundImage($widgetAsset);
        $images = self::resolveImages($widgetAsset);

        return new self(
            asset: $asset,
            color: is_string($color) && $color !== '' ? $color : $fallbackColor,
            linkedPage: $linkedPage instanceof Page ? $linkedPage : null,
            url: $linkedPage instanceof Page ? $linkedPage->pageUrl?->full_url : null,
            backgroundImage: $backgroundImage,
            images: $images,
        );
    }

    private static function resolveBackgroundImage(WidgetAsset $widgetAsset): ?Media
    {
        if ($widgetAsset->asset instanceof Media) {
            return $widgetAsset->asset;
        }

        $collection = MediaCollectionEnum::BackgroundImage->value;

        $media = $widgetAsset->media?->firstWhere('collection_name', $collection)
            ?? $widgetAsset->asset->media?->firstWhere('collection_name', $collection);

        return $media instanceof Media ? $media : null;
    }

    /**
     * @return Collection<int, Media>|null
     */
    private static function resolveImages(WidgetAsset $widgetAsset): ?Collection
    {
        if ($widgetAsset->asset instanceof Media) {
            return null;
        }

        $collection = MediaCollectionEnum::Image->value;

        $images = $widgetAsset->media?->where('collection_name', $collection);

        if (! $images?->isNotEmpty()) {
            $images = $widgetAsset->asset->media?->where('collection_name', $collection);
        }

        return $images?->isNotEmpty() ? $images->values() : null;
    }
}
