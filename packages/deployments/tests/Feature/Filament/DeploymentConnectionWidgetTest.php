<?php

declare(strict_types=1);

use Capell\Deployments\Filament\Widgets\DeploymentConnectionWidget;

it('DeploymentConnectionWidget class exists', function (): void {
    expect(class_exists(DeploymentConnectionWidget::class))->toBeTrue();
});
