<?php

declare(strict_types=1);

namespace Capell\PublicActions\Http\Middleware;

use Capell\PublicActions\Actions\ResolvePublicActionIntegrationTokenAction;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PublicActionZapierAuthMiddleware
{
    public function __construct(
        private readonly ResolvePublicActionIntegrationTokenAction $resolveToken,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $plainTextToken = $request->bearerToken() ?: $request->headers->get('X-Capell-Public-Actions-Token');
        $token = $this->resolveToken->handle(is_string($plainTextToken) ? $plainTextToken : null);

        if ($token === null) {
            return response()->json(['message' => __('capell-public-actions::generic.api.unauthorized')], 401);
        }

        $request->attributes->set('public_action_integration_token', $token);

        return $next($request);
    }
}
