<?php

use Capell\Core\Actions\ColorConverterAction;
use Capell\Core\Enums\DefaultColorEnum;
use Capell\Frontend\Facades\Frontend;

$theme = Frontend::theme();
$site = Frontend::site();

$brandColor = ColorConverterAction::run($site->getMeta('brand_color') ?: '#111827');
$linkColor = ColorConverterAction::run($theme->getMeta('link_color') ?: '#1d4ed8');
$linkColorActive = ColorConverterAction::run($theme->getMeta('link_color_active') ?: $theme->getMeta('link_color') ?: '#1e40af');
$dividerColor = ColorConverterAction::run($theme->getMeta('divider_color') ?: '#e5e7eb');

$isSafeToken = static fn (string $name, string $value): bool => preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]*$/', $name) === 1
    && preg_match('/[\x00-\x1F\x7F;{}<>]/', $value) !== 1;

$paletteColors = collect(DefaultColorEnum::getKeyValues())
    ->merge($theme->colors)
    ->map(function (mixed $value, string $name) use ($isSafeToken): ?array {
        if (! is_string($value) || ! $isSafeToken($name, $value)) {
            return null;
        }

        try {
            $convertedValue = ColorConverterAction::run($value);
        } catch (Throwable) {
            return null;
        }

        if (! is_string($convertedValue) || ! $isSafeToken($name, $convertedValue)) {
            return null;
        }

        return ['name' => $name, 'value' => $convertedValue];
    })
    ->filter()
    ->values();

?>

<style>
    :root {
        @foreach ($paletteColors as $paletteColor)
        --color-{{ $paletteColor['name'] }}: {{ $paletteColor['value'] }};
        @endforeach
        --color-brand: {{ $brandColor }};
        --color-link: {{ $linkColor }};
        --color-link-active: {{ $linkColorActive }};
        --color-divider: {{ $dividerColor }};
    }
</style>
