<?php

declare(strict_types=1);

namespace Capell\AccessGate\Tests\Fixtures\Autoload;

use Capell\AccessGate\Contracts\AccessRequestMethod;
use Capell\AccessGate\Models\Area;

final class PublicRequestProviderMethod implements AccessRequestMethod
{
    public function key(): string
    {
        return 'provider';
    }

    public function label(): string
    {
        return 'Continue with Provider';
    }

    public function description(): string
    {
        return 'Request access using the host application provider.';
    }

    public function isEnabled(Area $area): bool
    {
        return $area->key === 'preview';
    }

    public function isPrimary(Area $area): bool
    {
        return $area->key === 'preview';
    }

    public function url(Area $area, ?string $requestedUrl = null): string
    {
        return url('/host-provider/start?' . http_build_query([
            'area' => $area->key,
            'redirect' => $requestedUrl,
        ]));
    }
}
