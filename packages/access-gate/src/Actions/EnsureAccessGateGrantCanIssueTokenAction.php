<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Enums\AccessAreaStatus;
use Capell\AccessGate\Enums\GrantStatus;
use Capell\AccessGate\Models\Grant;
use LogicException;
use Lorisleiva\Actions\Concerns\AsAction;

final class EnsureAccessGateGrantCanIssueTokenAction
{
    use AsAction;

    public function handle(Grant $grant): Grant
    {
        $grant->loadMissing('area');

        if ($grant->area === null || $grant->area->status !== AccessAreaStatus::Active) {
            throw new LogicException('Access gate tokens can only be issued for active access areas.');
        }

        if ($grant->status !== GrantStatus::Active || $grant->revoked_at !== null) {
            throw new LogicException('Access gate tokens can only be issued for active grants.');
        }

        if ($grant->starts_at !== null && $grant->starts_at->isFuture()) {
            throw new LogicException('Access gate tokens cannot be issued before the grant starts.');
        }

        if ($grant->expires_at !== null && $grant->expires_at->isPast()) {
            throw new LogicException('Access gate tokens cannot be issued for expired grants.');
        }

        return $grant;
    }
}
