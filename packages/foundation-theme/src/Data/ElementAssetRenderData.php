<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\Data;

use Spatie\LaravelData\Data;

final class ElementAssetRenderData extends Data
{
    /**
     * @param  array<string, mixed>  $meta
     * @param  array<int|string, mixed>  $social
     * @param  array<int|string, mixed>  $tags
     */
    public function __construct(
        public readonly mixed $asset,
        public readonly mixed $image,
        public readonly mixed $linkedPage,
        public readonly mixed $translation,
        public readonly array $meta,
        public readonly string $alt,
        public readonly ?string $content,
        public readonly ?string $icon,
        public readonly ?string $position,
        public readonly array $social,
        public readonly array $tags,
        public readonly ?string $title,
    ) {}
}
