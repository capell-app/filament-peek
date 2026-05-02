<?php

declare(strict_types=1);

namespace Capell\Deployments\Filament\Widgets;

use Capell\Deployments\Models\DeploymentConnection;
use Filament\Widgets\Widget;

final class DeploymentConnectionWidget extends Widget
{
    protected string $view = 'capell-deployments::filament.widgets.deployment-connection';

    /** @var int|string|array<string, int|string|null> */
    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1];

    protected static ?int $sort = 12;

    public function getConnection(): ?DeploymentConnection
    {
        return DeploymentConnection::query()->where('is_active', true)->first();
    }
}
