<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\Support\View;

final class FoundationThemeViewName
{
    public static function canonical(string $view): string
    {
        foreach (self::legacyViewPrefixes() as $legacyPrefix => $canonicalPrefix) {
            if (str_starts_with($view, $legacyPrefix)) {
                return $canonicalPrefix . substr($view, strlen($legacyPrefix));
            }
        }

        return $view;
    }

    /**
     * @return array<string, string>
     */
    private static function legacyViewPrefixes(): array
    {
        return [];
    }
}
