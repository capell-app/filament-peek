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

    public function handle(Element $element, mixed $content, mixed $title, bool $rounded, mixed $reverseOrder): BannerImageRenderData
    {
        $backgroundImage = $this->firstLoadedElementMedia($element, MediaCollectionEnum::BackgroundImage->value)
            ?? $this->firstLoadedElementMedia($element, MediaCollectionEnum::Image->value)
            ?? $this->firstAssetMedia($element);

        $meta = is_array($element->meta) ? $element->meta : [];
        $actions = $meta['actions'] ?? null;
        $hasContent = filled($content) || filled($title) || filled($actions);

        return new BannerImageRenderData(
            backgroundImage: $backgroundImage,
            actions: $actions,
            hasContent: $hasContent,
            imageRoundedClass: $this->imageRoundedClass($rounded, $hasContent, (bool) $reverseOrder),
        );
    }

    private function firstLoadedElementMedia(Element $element, string $collectionName): ?Media
    {
        if (! $element->relationLoaded('media')) {
            return null;
        }

        $media = $element->getRelation('media');

        if (! $media instanceof Collection) {
            return null;
        }

        $match = $media->first(
            fn (mixed $media): bool => $media instanceof Media && $media->collection_name === $collectionName,
        );

        return $match instanceof Media ? $match : null;
    }

    private function firstAssetMedia(Element $element): mixed
    {
        if (! $element->relationLoaded('assets')) {
            return null;
        }

        $assets = $element->getRelation('assets');

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
