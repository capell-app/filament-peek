<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\Actions;

use Capell\Core\Contracts\Pageable;
use Capell\Core\Enums\MediaCollectionEnum;
use Capell\Core\Models\Media;
use Capell\FoundationTheme\Data\ElementAssetRenderData;
use Capell\LayoutBuilder\Models\ElementAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsObject;

final class BuildElementAssetRenderDataAction
{
    use AsObject;

    public function handle(ElementAsset $widgetAsset): ElementAssetRenderData
    {
        $asset = $this->loadedRelation($widgetAsset, 'asset');
        $translation = $asset instanceof Model ? $this->loadedRelation($asset, 'translation') : null;
        $meta = is_array(data_get($asset, 'meta')) ? data_get($asset, 'meta') : [];

        return new ElementAssetRenderData(
            asset: $asset,
            image: $this->image($widgetAsset, $asset),
            linkedPage: $this->linkedPage($widgetAsset, $asset),
            translation: $translation,
            meta: $meta,
            alt: $this->stringValue($translation, 'label') ?? $this->stringValue($translation, 'title') ?? '',
            content: $this->stringValue($translation, 'content'),
            icon: $this->metaString($asset, 'icon'),
            position: $this->metaString($asset, 'position'),
            social: $this->metaArray($asset, 'social'),
            tags: $this->metaArray($asset, 'tags'),
            title: $this->stringValue($translation, 'title'),
        );
    }

    private function image(ElementAsset $widgetAsset, mixed $asset): ?Media
    {
        return $this->firstLoadedMedia($widgetAsset)
            ?? ($asset instanceof Model ? $this->firstLoadedMedia($asset) : null)
            ?? ($asset instanceof Model ? $this->loadedImage($asset) : null);
    }

    private function linkedPage(ElementAsset $widgetAsset, mixed $asset): mixed
    {
        if ($asset instanceof Pageable) {
            return $asset;
        }

        if ($asset instanceof Model) {
            return $this->loadedRelation($asset, 'linkedPage');
        }

        return $this->loadedRelation($widgetAsset, 'linkedPage');
    }

    private function loadedImage(Model $model): ?Media
    {
        $image = $this->loadedRelation($model, 'image');

        return $image instanceof Media ? $image : null;
    }

    private function firstLoadedMedia(Model $model): ?Media
    {
        $media = $this->loadedRelation($model, 'media');

        if (! $media instanceof Collection) {
            return null;
        }

        $match = $media->first(
            static fn (mixed $media): bool => $media instanceof Media
                && in_array($media->collection_name, [
                    MediaCollectionEnum::Image->value,
                    MediaCollectionEnum::BackgroundImage->value,
                ], true),
        );

        if ($match instanceof Media) {
            return $match;
        }

        $fallback = $media->first(static fn (mixed $media): bool => $media instanceof Media);

        return $fallback instanceof Media ? $fallback : null;
    }

    private function loadedRelation(Model $model, string $relation): mixed
    {
        if (! $model->relationLoaded($relation)) {
            return null;
        }

        return $model->getRelation($relation);
    }

    private function metaString(mixed $asset, string $key): ?string
    {
        if (! is_object($asset) || ! method_exists($asset, 'getMeta')) {
            return null;
        }

        $value = $asset->getMeta($key);

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * @return array<int|string, mixed>
     */
    private function metaArray(mixed $asset, string $key): array
    {
        if (! is_object($asset) || ! method_exists($asset, 'getMeta')) {
            return [];
        }

        $value = $asset->getMeta($key, []);

        return is_array($value) ? $value : [];
    }

    private function stringValue(mixed $object, string $key): ?string
    {
        $value = data_get($object, $key);

        return is_string($value) && $value !== '' ? $value : null;
    }
}
