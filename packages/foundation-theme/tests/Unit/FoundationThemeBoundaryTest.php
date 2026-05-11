<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;

it('owns the opinionated public body behavior', function (): void {
    $body = file_get_contents(dirname(__DIR__, 2) . '/resources/views/components/app/body.blade.php');

    expect($body)->toContain('font-sans')
        ->and($body)->toContain('dark:bg-gray-950')
        ->and($body)->toContain('showLightbox');
});

it('owns the opinionated content prose and divider behavior', function (): void {
    $content = file_get_contents(dirname(__DIR__, 2) . '/resources/views/components/content.blade.php');
    $themeCss = file_get_contents(dirname(__DIR__, 2) . '/resources/css/theme/theme.css');

    expect($content)->toContain('content-component prose')
        ->and($content)->toContain('prose-invert')
        ->and($content)->toContain('prose-muted')
        ->and($content)->toContain('var(--color-divider)')
        ->and($themeCss)->toContain('.prose-muted')
        ->and($themeCss)->toContain('.prose-compact');
});

it('owns the token-backed link utilities', function (): void {
    $themeCss = file_get_contents(dirname(__DIR__, 2) . '/resources/css/theme/theme.css');

    expect($themeCss)->toContain('.text-brand')
        ->and($themeCss)->toContain('--color-brand')
        ->and($themeCss)->toContain('--color-link');
});

it('overrides the frontend body and content components', function (): void {
    $bodyPath = realpath(View::getFinder()->find('capell::components.app.body'));
    $contentPath = realpath(View::getFinder()->find('capell::components.content'));

    expect($bodyPath)->toContain('packages/foundation-theme/resources/views/components/app/body.blade.php')
        ->and($contentPath)->toContain('packages/foundation-theme/resources/views/components/content.blade.php');
});
