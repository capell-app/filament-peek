<?php

declare(strict_types=1);

namespace Capell\Admin\Filament\Widgets;

use Filament\Widgets\Widget;

abstract class CapellWidget extends Widget
{
    /** @var list<string> */
    protected static array $rolesConfigKeys = [];

    protected static string $settingsKey = '';

    public static function canView(): bool {}

    public static function settingsKey(): string {}

    /** @return list<string> */
    public static function rolesConfigKeys(): array {}
}
