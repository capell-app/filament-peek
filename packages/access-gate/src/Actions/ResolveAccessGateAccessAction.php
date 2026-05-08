<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Data\AccessGateAccessResultData;
use Capell\AccessGate\Enums\AccessAreaStatus;
use Capell\AccessGate\Enums\BrowserTokenStatus;
use Capell\AccessGate\Enums\GrantStatus;
use Capell\AccessGate\Enums\GrantSubjectType;
use Capell\AccessGate\Enums\IdentityMode;
use Capell\AccessGate\Models\Area;
use Capell\AccessGate\Models\BrowserToken;
use Capell\AccessGate\Models\Grant;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class ResolveAccessGateAccessAction
{
    use AsAction;

    /**
     * @param  list<string>  $areaKeys
     */
    public function handle(Request $request, array $areaKeys): AccessGateAccessResultData
    {
        $areas = Area::query()
            ->whereIn('key', $areaKeys)
            ->where('status', AccessAreaStatus::Active->value)
            ->get();

        foreach ($areas as $area) {
            $result = $this->resolveArea($request, $area);

            if ($result->allowed) {
                return $result;
            }
        }

        return new AccessGateAccessResultData(false);
    }

    private function resolveArea(Request $request, Area $area): AccessGateAccessResultData
    {
        if ($area->identity_mode === IdentityMode::Authenticated || $area->identity_mode === IdentityMode::Hybrid) {
            $grant = $this->activeUserGrant($area, $request->user()?->getAuthIdentifier());

            if ($grant !== null) {
                return new AccessGateAccessResultData(true, $area, $grant);
            }
        }

        if ($area->identity_mode === IdentityMode::GuestLink || $area->identity_mode === IdentityMode::Hybrid) {
            $browserToken = $this->activeBrowserToken($area, $this->plainBrowserToken($request));

            if ($browserToken !== null) {
                $browserToken->forceFill(['last_used_at' => now()])->save();

                return new AccessGateAccessResultData(true, $area, $browserToken->grant, $browserToken);
            }
        }

        return new AccessGateAccessResultData(false, $area);
    }

    private function activeUserGrant(Area $area, mixed $userId): ?Grant
    {
        if (! is_int($userId) && ! is_string($userId)) {
            return null;
        }

        return Grant::query()
            ->where('access_area_id', $area->getKey())
            ->where('subject_type', GrantSubjectType::User->value)
            ->where('subject_id', (string) $userId)
            ->where('status', GrantStatus::Active->value)
            ->whereNull('revoked_at')
            ->where(function ($query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    private function activeBrowserToken(Area $area, ?string $plainTextToken): ?BrowserToken
    {
        if ($plainTextToken === null || $plainTextToken === '') {
            return null;
        }

        return BrowserToken::query()
            ->where('access_area_id', $area->getKey())
            ->where('token_hash', hash('sha256', $plainTextToken))
            ->where('status', BrowserTokenStatus::Active->value)
            ->whereNull('revoked_at')
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->whereHas('grant', function ($query): void {
                $query
                    ->where('status', GrantStatus::Active->value)
                    ->whereNull('revoked_at')
                    ->where(function ($grantQuery): void {
                        $grantQuery->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($grantQuery): void {
                        $grantQuery->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    });
            })
            ->first();
    }

    private function plainBrowserToken(Request $request): ?string
    {
        $cookieName = config('access-gate.cookies.browser_token.name', 'capell_access_gate_browser_token');

        return is_string($cookieName) ? $request->cookies->get($cookieName) : null;
    }
}
