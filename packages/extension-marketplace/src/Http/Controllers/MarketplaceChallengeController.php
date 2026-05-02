<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Http\Controllers;

use Capell\ExtensionMarketplace\Enums\MarketplaceRegistrationStatus;
use Capell\ExtensionMarketplace\Models\MarketplaceRegistrationSession;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

final class MarketplaceChallengeController
{
    public function __invoke(Request $request, string $challengeId): Response
    {
        $host = Str::lower(rtrim($request->getHost(), '.'));

        $session = MarketplaceRegistrationSession::query()
            ->where('challenge_id', $challengeId)
            ->where('domain', $host)
            ->where('status', MarketplaceRegistrationStatus::Pending)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        abort_if($session === null, 404);

        return response($session->challenge_token, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Cache-Control', 'no-store');
    }
}
