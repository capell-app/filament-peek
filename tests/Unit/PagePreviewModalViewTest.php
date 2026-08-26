<?php

declare(strict_types=1);

it('renders labelled device controls instead of icon-only buttons', function (): void {
    $html = view('capell-filament-peek::page-preview-modal')->render();

    expect($html)
        ->toContain(__('capell-filament-peek::actions.preview.devices.desktop'))
        ->toContain(__('capell-filament-peek::actions.preview.devices.tablet'))
        ->toContain(__('capell-filament-peek::actions.preview.devices.mobile'))
        ->toContain(__('capell-filament-peek::actions.preview.rotate'));
});

it('exposes every configured toolbar label on the Alpine component state', function (): void {
    $html = view('capell-filament-peek::page-preview-modal')->render();

    expect($html)
        ->toContain('closeLabel: config.closeLabel')
        ->toContain('loadingLabel: config.loadingLabel')
        ->toContain('unavailableLabel: config.unavailableLabel')
        ->toContain('recoverLabel: config.recoverLabel')
        ->toContain('rotateLabel: config.rotateLabel')
        ->toContain('deviceGroupLabel: config.deviceGroupLabel');
});

it('renders an accessible, keyboard-reachable dialog with a labelled close action', function (): void {
    $html = view('capell-filament-peek::page-preview-modal')->render();

    expect($html)
        ->toContain('role="dialog"')
        ->toContain('aria-modal="true"')
        ->toContain('aria-labelledby="capell-peek-title"')
        ->toContain('x-trap="open"')
        ->toContain('x-on:keydown.escape.window="close()"')
        ->toContain('x-bind:aria-label="closeLabel"');
});

it('respects prefers-reduced-motion for the preview modal chrome', function (): void {
    $html = view('capell-filament-peek::page-preview-modal')->render();

    expect($html)->toContain('prefers-reduced-motion: reduce');
});

it('sizes device presets responsively instead of a fixed pixel box', function (): void {
    $html = view('capell-filament-peek::page-preview-modal')->render();

    expect($html)->toContain('min(${width}, 100%)')
        ->toContain('min(${height}, 100%)');
});

it('restores focus to the invoking action and re-triggers it for recovery', function (): void {
    $html = view('capell-filament-peek::page-preview-modal')->render();

    expect($html)
        ->toContain('previouslyFocused')
        ->toContain("component.call('mountAction', 'peekPagePreview')");
});

it('detects a failed or expired render without exposing the signed preview url', function (): void {
    $html = view('capell-filament-peek::page-preview-modal')->render();

    expect($html)
        ->toContain('data-capell-preview-ribbon')
        ->not->toContain('temporary_signed');
});
