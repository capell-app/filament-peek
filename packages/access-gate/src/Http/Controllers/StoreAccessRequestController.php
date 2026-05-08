<?php

declare(strict_types=1);

namespace Capell\AccessGate\Http\Controllers;

use Capell\AccessGate\Actions\CreateRegistrationAction;
use Capell\AccessGate\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class StoreAccessRequestController
{
    public function __construct(
        private readonly CreateRegistrationAction $createRegistration,
    ) {}

    public function __invoke(Request $request, string $area): RedirectResponse
    {
        $accessArea = Area::query()->where('key', $area)->firstOrFail();

        $this->createRegistration->handle($accessArea, [
            ...$request->except('_token'),
            'metadata' => [
                'ip_hash' => hash('sha256', (string) $request->ip()),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return $this->noStore(
            redirect()
                ->route('capell-access-gate.request', ['area' => $accessArea->key])
                ->with('access_gate_status', __('capell-access-gate::public.request_submitted')),
        );
    }

    private function noStore(RedirectResponse $response): RedirectResponse
    {
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
