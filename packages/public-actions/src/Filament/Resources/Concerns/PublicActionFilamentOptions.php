<?php

declare(strict_types=1);

namespace Capell\PublicActions\Filament\Resources\Concerns;

use BackedEnum;

trait PublicActionFilamentOptions
{
    /**
     * @param  class-string<BackedEnum>  $enum
     * @return array<string, string>
     */
    protected static function enumOptions(string $enum): array
    {
        return collect($enum::cases())
            ->mapWithKeys(fn (BackedEnum $case): array => [(string) $case->value => method_exists($case, 'getLabel') ? $case->getLabel() : (string) $case->value])
            ->all();
    }
}
