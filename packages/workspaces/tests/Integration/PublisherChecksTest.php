<?php

declare(strict_types=1);

namespace Capell\Workspaces\Tests\Integration;

use Capell\Workspaces\Checks\PublishCheck;
use Capell\Workspaces\Checks\PublishCheckResult;
use Capell\Workspaces\Checks\PublishCheckSeverity;
use Capell\Workspaces\Enums\WorkspaceStatusEnum;
use Capell\Workspaces\Exceptions\PublishBlockedByChecksException;
use Capell\Workspaces\Models\Version;
use Capell\Workspaces\Models\Workspace;
use Capell\Workspaces\Publisher;
use Illuminate\Support\Facades\Config;

class AlwaysBlockingCheck implements PublishCheck
{
    public function identifier(): string
    {
        return 'always-blocking';
    }

    public function label(): string
    {
        return 'Always blocking';
    }

    public function run(Workspace $workspace): PublishCheckResult
    {
        return new PublishCheckResult(
            identifier: $this->identifier(),
            label: $this->label(),
            severity: PublishCheckSeverity::Error,
            messages: ['blocked'],
        );
    }
}

it('aborts publish when an error-severity check finds issues', function (): void {
    Config::set('capell.workspaces.publish_checks', [AlwaysBlockingCheck::class]);

    $workspace = Workspace::factory()->create(['status' => WorkspaceStatusEnum::Approved]);

    expect(fn () => app(Publisher::class)->publish($workspace))
        ->toThrow(PublishBlockedByChecksException::class);
});

it('allows publish when bypassChecks is true even with blocking errors', function (): void {
    Config::set('capell.workspaces.publish_checks', [AlwaysBlockingCheck::class]);

    $workspace = Workspace::factory()->create(['status' => WorkspaceStatusEnum::Approved]);

    $version = app(Publisher::class)->publish($workspace, bypassChecks: true);

    expect($version)->toBeInstanceOf(Version::class);
});

it('dryRun includes check results in the report', function (): void {
    Config::set('capell.workspaces.publish_checks', [AlwaysBlockingCheck::class]);

    $workspace = Workspace::factory()->create(['status' => WorkspaceStatusEnum::Approved]);

    $report = app(Publisher::class)->dryRun($workspace);

    expect($report->checkResults)->toHaveCount(1)
        ->and($report->hasBlockingCheckErrors())->toBeTrue();
});
