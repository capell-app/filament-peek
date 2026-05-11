<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;

it('owns the opinionated public head behavior', function (): void {
    $component = file_get_contents(dirname(__DIR__, 2) . '/resources/views/components/app/head/custom.blade.php');

    expect($component)->toContain('localStorage.theme')
        ->and($component)->toContain('prefers-color-scheme: dark')
        ->and($component)->toContain('updateHeaderSticky')
        ->and($component)->toContain('--color-brand');
});

it('overrides the frontend custom head component', function (): void {
    $path = realpath(View::getFinder()->find('capell::components.app.head.custom'));

    expect($path)->toContain('packages/foundation-theme/resources/views/components/app/head/custom.blade.php');
});
