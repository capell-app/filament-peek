<?php

declare(strict_types=1);

use Capell\Admin\Contracts\DashboardSettingsContributor;
use Capell\Reports\Filament\Settings\Contributors\ReportsDashboardSettingsContributor;
use Capell\Reports\Tests\ReportsTestCase;

uses(ReportsTestCase::class);

it('exposes reports dashboard settings keys with translated labels', function (): void {
    $entries = (new ReportsDashboardSettingsContributor)->settingsKeys();

    expect(collect($entries)->pluck('key')->all())->toBe([
        'publishing_trend',
        'content_health',
    ]);

    foreach ($entries as $entry) {
        expect($entry['label'])->toBeString()->not->toBe('')
            ->and(str_contains($entry['label'], 'capell-reports::'))->toBeFalse()
            ->and($entry['group'])->toBe(__('capell-reports::dashboard.group_reports'));
    }
});

it('registers the reports dashboard settings contributor', function (): void {
    $contributors = collect(app()->tagged(DashboardSettingsContributor::TAG))
        ->map(fn (DashboardSettingsContributor $contributor): string => $contributor::class);

    expect($contributors)->toContain(ReportsDashboardSettingsContributor::class);
});
