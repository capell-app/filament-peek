<?php

declare(strict_types=1);

namespace Capell\Deployments\Enums;

use Filament\Support\Contracts\HasLabel;

enum InstallPolicy: string implements HasLabel
{
    case DirectCommit = 'direct_commit';
    case PullRequestAutoMerge = 'pr_auto_merge';
    case PullRequestManual = 'pr_manual_review';

    public function getLabel(): string
    {
        return match ($this) {
            self::DirectCommit => 'Direct commit (fastest)',
            self::PullRequestAutoMerge => 'Pull request, auto-merge on green CI (recommended)',
            self::PullRequestManual => 'Pull request, manual review (most cautious)',
        };
    }
}
