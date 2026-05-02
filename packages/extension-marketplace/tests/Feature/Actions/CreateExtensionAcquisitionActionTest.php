<?php

declare(strict_types=1);

use Capell\ExtensionMarketplace\Actions\CreateExtensionAcquisitionAction;
use Capell\ExtensionMarketplace\Data\ExtensionListingData;
use Illuminate\Support\Facades\Http;

it('returns composer acquisition instructions without creating install intents', function (): void {
    $listing = new ExtensionListingData(
        slug: 'seo-tools',
        name: 'SEO Tools',
        composerName: 'capell-app/seo-tools',
        kind: 'package',
        description: null,
        priceCents: 0,
        isPaid: false,
        forkRepoUrl: null,
        productId: null,
        latestVersion: '2.1.0',
    );

    config([
        'capell-extension-marketplace.instance.id' => '00000000-0000-4000-8000-000000000001',
        'capell-extension-marketplace.marketplace.base_url' => 'https://marketplace.test/api',
        'capell-extension-marketplace.marketplace.webhook_secret' => 'test-secret',
    ]);

    Http::fake([
        'https://marketplace.test/api/extensions/seo-tools/install-authorization' => Http::response([
            'data' => [
                'composer_name' => 'capell-app/seo-tools',
                'version_constraint' => '^2.1',
            ],
        ]),
    ]);

    $acquisition = CreateExtensionAcquisitionAction::run($listing);

    expect($acquisition->composerCommand)->toBe('composer require capell-app/seo-tools:^2.1')
        ->and($acquisition->requiresDeployment)->toBeFalse();
});
