<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\Actions;

use Capell\Core\Contracts\Pageable;
use Capell\Core\Enums\MediaCollectionEnum;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Models\Media;
use Capell\FoundationTheme\Data\AssetBannerItemData;
use Capell\LayoutBuilder\Models\Element;
use Capell\LayoutBuilder\Models\ElementAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsObject;
use Throwable;

final class BuildAssetBannerItemsAction
{
    use AsObject;

    /**
     * @return Collection<int, AssetBannerItemData>
     */
    public function handle(Element $element): Collection
    {
        $elementAssets = $element->relationLoaded('assets') ? $element->getRelation('assets') : collect();

        if (! $elementAssets instanceof Collection) {
            return collect();
        }

        return $elementAssets
            ->filter(fn (mixed $elementAsset): bool => $elementAsset instanceof ElementAsset)
            ->map(fn (ElementAsset $elementAsset): AssetBannerItemData => $this->item($element, $elementAsset))
            ->values();
    }

    private function item(Element $element, ElementAsset $elementAsset): AssetBannerItemData
    {
        $asset = $elementAsset->relationLoaded('asset') ? $elementAsset->getRelation('asset') : null;
        $linkedPage = $this->linkedPage($elementAsset, $asset);
        $translation = $asset instanceof Model && $asset->relationLoaded('translation')
            ? $asset->getRelation('translation')
            : null;

        $assetDefinition = $this->assetDefinition($elementAsset);
        $hasTranslations = $asset instanceof Model
            && is_object($assetDefinition)
            && (bool) ($assetDefinition->hasTranslations ?? false);

        return new AssetBannerItemData(
            image: $this->image($element, $elementAsset, $asset),
            alt: (string) ($translation?->label ?? $translation?->title ?? ''),
            title: $hasTranslations ? $translation?->title : null,
            content: $hasTranslations ? $translation?->content : null,
            url: $this->pageUrl($linkedPage),
            linkText: $this->linkText($linkedPage),
        );
    }

    private function image(Element $element, ElementAsset $elementAsset, mixed $asset): mixed
    {
        return $this->firstLoadedMedia($elementAsset, MediaCollectionEnum::Image->value)
            ?? ($asset instanceof Model && $asset->relationLoaded('image') ? $asset->getRelation('image') : null)
            ?? $this->firstLoadedMedia($element, MediaCollectionEnum::BackgroundImage->value);
    }

    private function linkedPage(ElementAsset $elementAsset, mixed $asset): mixed
    {
        if ($asset instanceof Pageable) {
            return $asset;
        }

        if ($asset instanceof Model && $asset->relationLoaded('linkedPage')) {
            return $asset->getRelation('linkedPage');
        }

        return $elementAsset->relationLoaded('linkedPage') ? $elementAsset->getRelation('linkedPage') : null;
    }

    private function pageUrl(mixed $linkedPage): ?string
    {
        if (! $linkedPage instanceof Model || ! $linkedPage->relationLoaded('pageUrl')) {
            return null;
        }

        $pageUrl = $linkedPage->getRelation('pageUrl');

        return is_string($pageUrl?->full_url ?? null) ? $pageUrl->full_url : null;
    }

    private function linkText(mixed $linkedPage): ?string
    {
        if (! $linkedPage instanceof Model || ! $linkedPage->relationLoaded('translation')) {
            return null;
        }

        $translation = $linkedPage->getRelation('translation');

        return is_string($translation?->link_text ?? null) ? $translation->link_text : null;
    }

    private function firstLoadedMedia(Model $model, string $collectionName): ?Media
    {
        if (! $model->relationLoaded('media')) {
            return null;
        }

        $media = $model->getRelation('media');

        if (! $media instanceof Collection) {
            return null;
        }

        $match = $media->first(
            fn (mixed $media): bool => $media instanceof Media && $media->collection_name === $collectionName,
        );

        return $match instanceof Media ? $match : null;
    }

    private function assetDefinition(ElementAsset $elementAsset): mixed
    {
        if (! is_string($elementAsset->asset_type) || $elementAsset->asset_type === '') {
            return null;
        }

        try {
            return CapellCore::getAsset($elementAsset->asset_type);
        } catch (Throwable) {
            return null;
        }
    }
}
