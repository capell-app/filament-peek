<?php

declare(strict_types=1);

namespace Capell\Address\Support;

class FlagIconRenderer
{
    private const DEFAULT_STYLE = '4x3';

    private const VALID_ICON_PATTERN = '/\A(?:1x1|4x3)-[a-z0-9]+(?:-[a-z0-9]+)*\z/';

    private const VALID_STYLE_PATTERN = '/\A(?:1x1|4x3)\z/';

    public function assetPath(?string $flag, string $style = self::DEFAULT_STYLE): ?string
    {
        $icon = $this->iconName($flag, $style);

        if ($icon === null) {
            return null;
        }

        $asset = 'vendor/blade-country-flags/' . $icon . '.svg';

        if (! file_exists(public_path($asset))) {
            return null;
        }

        return $asset;
    }

    public function fallbackLabel(?string $flag, ?string $label = null, string $style = self::DEFAULT_STYLE): string
    {
        if (is_string($label) && trim($label) !== '') {
            return trim($label);
        }

        $icon = $this->iconName($flag, $style);

        if ($icon === null) {
            return '';
        }

        return strtoupper(preg_replace('/\A(?:1x1|4x3)-/', '', $icon) ?? '');
    }

    public function iconName(?string $flag, string $style = self::DEFAULT_STYLE): ?string
    {
        if (! is_string($flag)) {
            return null;
        }

        $flag = strtolower(trim($flag));
        $style = strtolower(trim($style));

        if ($flag === '') {
            return null;
        }

        if (str_starts_with($flag, 'flag-')) {
            $flag = substr($flag, 5);
        }

        if (preg_match(self::VALID_ICON_PATTERN, $flag) === 1) {
            return $flag;
        }

        if (preg_match(self::VALID_STYLE_PATTERN, $style) !== 1) {
            $style = self::DEFAULT_STYLE;
        }

        $countryCode = trim(preg_replace('/[^a-z0-9-]+/', '-', $flag) ?? '', '-');

        if ($countryCode === '') {
            return null;
        }

        return $style . '-' . $countryCode;
    }
}
