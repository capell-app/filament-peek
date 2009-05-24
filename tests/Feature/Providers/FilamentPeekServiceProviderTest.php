<?php

declare(strict_types=1);

use Capell\Admin\Contracts\Extenders\AdminPanelExtender;
use Capell\Admin\Contracts\Extenders\PagePreviewActionExtender;
use Capell\Core\Facades\CapellCore;
use Capell\FilamentPeek\Filament\Extenders\FilamentPeekPanelExtender;
use Capell\FilamentPeek\Filament\Extenders\PagePeekPreviewActionExtender;
use Capell\FilamentPeek\Providers\FilamentPeekServiceProvider;

it('does not tag either extender when the package is not installed', function (): void {
    $tagsProperty = new ReflectionProperty(app(), 'tags');
    $originalTags = $tagsProperty->getValue(app());
    throw_unless(is_array($originalTags), RuntimeException::class, 'Expected the application container tags to be an array.');

    $tags = $originalTags;
    $tags[AdminPanelExtender::TAG] = array_values(array_filter(
        (array) ($tags[AdminPanelExtender::TAG] ?? []),
        static fn (mixed $abstract): bool => $abstract !== FilamentPeekPanelExtender::class,
    ));
    $tags[PagePreviewActionExtender::TAG] = array_values(array_filter(
        (array) ($tags[PagePreviewActionExtender::TAG] ?? []),
        static fn (mixed $abstract): bool => $abstract !== PagePeekPreviewActionExtender::class,
    ));

    $tagsProperty->setValue(app(), $tags);
    CapellCore::forcePackageInstalled(FilamentPeekServiceProvider::$packageName, false);

    try {
        $provider = app()->getProvider(FilamentPeekServiceProvider::class);
        throw_unless($provider instanceof FilamentPeekServiceProvider, RuntimeException::class, 'Expected the Filament Peek service provider to be loaded.');

        $provider->registeringPackage();

        $hasPanelExtender = false;

        foreach (app()->tagged(AdminPanelExtender::TAG) as $extender) {
            if ($extender instanceof FilamentPeekPanelExtender) {
                $hasPanelExtender = true;
                break;
            }
        }

        $hasPreviewExtender = false;

        foreach (app()->tagged(PagePreviewActionExtender::TAG) as $extender) {
            if ($extender instanceof PagePeekPreviewActionExtender) {
                $hasPreviewExtender = true;
                break;
            }
        }

        expect($hasPanelExtender)->toBeFalse()
            ->and($hasPreviewExtender)->toBeFalse();
    } finally {
        $tagsProperty->setValue(app(), $originalTags);
        CapellCore::forcePackageInstalled(FilamentPeekServiceProvider::$packageName);
    }
});

it('tags both extenders when the package is installed and enabled', function (): void {
    CapellCore::forcePackageInstalled(FilamentPeekServiceProvider::$packageName, true);

    try {
        $provider = app()->getProvider(FilamentPeekServiceProvider::class);
        throw_unless($provider instanceof FilamentPeekServiceProvider, RuntimeException::class, 'Expected the Filament Peek service provider to be loaded.');

        $provider->registeringPackage();

        $hasPanelExtender = false;

        foreach (app()->tagged(AdminPanelExtender::TAG) as $extender) {
            if ($extender instanceof FilamentPeekPanelExtender) {
                $hasPanelExtender = true;
                break;
            }
        }

        expect($hasPanelExtender)->toBeTrue();
    } finally {
        CapellCore::forcePackageInstalled(FilamentPeekServiceProvider::$packageName);
    }
});
