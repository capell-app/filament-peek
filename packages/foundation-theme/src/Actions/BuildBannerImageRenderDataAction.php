<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\Actions;

use Capell\Core\Enums\MediaCollectionEnum;
use Capell\Core\Models\Media;
use Capell\FoundationTheme\Data\BannerImageRenderData;
use Capell\LayoutBuilder\Models\Element;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsObject;

final class BuildBannerImageRenderDataAction
{
    use AsObject;

    public function handle(Element $widget, mixed $content, mixed $title, bool $rounded, mixed $reverseOrder): BannerImageRenderData
    {
        $backgroundImage = $this->firstLoadedElementMedia($widget, MediaCollectionEnum::BackgroundImage->value)
            ?? $this->firstLoadedElementMedia($widget, MediaCollectionEnum::Image->value)
            ?? $this->firstAssetMedia($widget);

        $meta = is_array($widget->meta) ? $widget->meta : [];
        $actions = $meta['actions'] ?? null;
        $hasContent = $content || $title || $actions;

        return new BannerImageRenderData(
            backgroundImage: $backgroundImage,
            actions: $actions,
            hasContent: (bool) $hasContent,
            imageRoundedClass: $this->imageRoundedClass($rounded, (bool) $hasContent, (bool) $reverseOrder),
        );
    }

    private function firstLoadedElementMedia(Element $widget, string $collectionName): ?Media
    {
        if (! $widget->relationLoaded('media')) {
            return null;
        }

        $media = $widget->getRelation('media');

        if (! $media instanceof Collection) {
            return null;
        }

        $match = $media->first(
            fn (mixed $media): bool => $media instanceof Media && $media->collection_name === $collectionName,
        );

        return $match instanceof Media ? $match : null;
    }

    private function firstAssetMedia(Element $widget): mixed
    {
        if (! $widget->relationLoaded('assets')) {
            return null;
        }

        $assets = $widget->getRelation('assets');

        if (! method_exists($assets, 'first')) {
            return null;
        }

        $firstAsset = $assets->first();

        if (! is_object($firstAsset) || ! method_exists($firstAsset, 'relationLoaded') || ! $firstAsset->relationLoaded('media')) {
            return null;
        }

        $media = $firstAsset->getRelation('media');

        return method_exists($media, 'first') ? $media->first() : null;
    }

    private function imageRoundedClass(bool $rounded, bool $hasContent, bool $reverseOrder): string
    {
        if (! $rounded) {
            return '';
        }

        if (! $hasContent) {
            return ' rounded-lg';
        }

        return $reverseOrder ? ' rounded-r-lg' : ' rounded-l-lg';
    }
}
