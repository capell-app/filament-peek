<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Http\Middleware;

use Capell\PublishingStudio\Models\PreviewLink;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\WorkspaceContext;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Resolves a workspace to apply as the current context for the request.
 *
 * Precedence:
 *  1. Signed URL with `__workspace=<uuid>` query parameter — the primary
 *     mechanism for shareable previews. Drops a short-lived cookie so the
 *     preview survives navigation.
 *  2. Existing `cms_workspace` cookie (previously issued by the same signed
 *     flow or by the admin panel's switcher).
 *  3. Authenticated admin session attribute `cms_workspace_id` (the
 *     Filament switcher sets this).
 *  4. None → context remains live.
 *
 * Any permissions check is the responsibility of the gate / policy layer —
 * this middleware only resolves the identifier.
 */
final class ResolveWorkspaceContext
{
    public const string QUERY_PARAM = '__workspace';

    public const string TOKEN_PARAM = '__pl';

    public const string COOKIE_NAME = 'cms_workspace';

    public const int COOKIE_TTL_MINUTES = 240;

    public const string SESSION_KEY = 'cms_workspace_id';

    private const string COOKIE_VERSION = 'v1';

    public function handle(Request $request, Closure $next): Response
    {
        $workspace = $this->resolve($request);
        $previousCacheDisabled = config('capell-core.disable_cache');
        $previousWorkspace = WorkspaceContext::current();

        WorkspaceContext::set($workspace);

        if ($workspace instanceof Workspace) {
            config(['capell-core.disable_cache' => true]);
        }

        try {
            $response = $next($request);
        } finally {
            WorkspaceContext::set($previousWorkspace);
            config(['capell-core.disable_cache' => $previousCacheDisabled]);
        }

        if ($workspace instanceof Workspace
            && $request->hasValidSignature()
            && $request->query(self::QUERY_PARAM) !== null) {
            $cookieValue = $this->signedCookieValue($request, $workspace);

            if ($cookieValue !== null) {
                $response->headers->setCookie(cookie(
                    self::COOKIE_NAME,
                    $cookieValue,
                    self::COOKIE_TTL_MINUTES,
                    null,
                    null,
                    $request->isSecure(),
                    true,
                    false,
                    'lax',
                ));
            }
        }

        if ($workspace instanceof Workspace) {
            $response->headers->set('Cache-Control', 'private, no-store, no-cache, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }

    private function resolve(Request $request): ?Workspace
    {
        if ($request->hasValidSignature()) {
            $uuid = $request->query(self::QUERY_PARAM);
            if (is_string($uuid) && $uuid !== '') {
                $token = $request->query(self::TOKEN_PARAM);
                $previewLinksAvailable = $this->tableExists('preview_links');

                if (is_string($token) && $token !== '' && $previewLinksAvailable) {
                    $link = PreviewLink::query()->where('token', $token)->first();
                    if (! $link instanceof PreviewLink || ! $link->isUsable()) {
                        return null;
                    }

                    $workspace = Workspace::query()->whereKey($link->workspace_id)->first();
                    if (! $workspace instanceof Workspace || $workspace->uuid !== $uuid) {
                        return null;
                    }

                    $link->forceFill([
                        'last_accessed_at' => CarbonImmutable::now(),
                        'access_count' => $link->access_count + 1,
                    ])->save();

                    return $workspace;
                }

                if ($previewLinksAvailable) {
                    return null;
                }

                $workspace = Workspace::query()->where('uuid', $uuid)->first();
                if ($workspace instanceof Workspace) {
                    return $workspace;
                }
            }
        }

        $cookieUuid = $this->verifiedCookieUuid($request);
        if ($cookieUuid !== null) {
            $workspace = Workspace::query()->where('uuid', $cookieUuid)->first();
            if ($workspace instanceof Workspace && $this->userMayResolve($request, $workspace)) {
                return $workspace;
            }
        }

        if ($request->hasSession()) {
            $sessionId = $request->session()->get(self::SESSION_KEY);
            if (is_int($sessionId) || (is_string($sessionId) && ctype_digit($sessionId))) {
                $workspace = Workspace::query()->find((int) $sessionId);
                if ($workspace instanceof Workspace && $this->userMayResolve($request, $workspace)) {
                    return $workspace;
                }
            }
        }

        return null;
    }

    /**
     * Authenticated users must have permission to view the workspace
     * before a stored cookie/session context is trusted — this prevents
     * a user whose workspace access was revoked from continuing to
     * operate under that context. Guests are allowed through so that
     * the server-issued signed-preview cookie flow continues to work.
     */
    private function userMayResolve(Request $request, Workspace $workspace): bool
    {
        $user = $request->user();

        if ($user === null) {
            return true;
        }

        try {
            return $user->can('view', $workspace);
        } catch (Throwable) {
            return false;
        }
    }

    private function signedCookieValue(Request $request, Workspace $workspace): ?string
    {
        $context = $this->cookieSignatureContext($request);

        if ($context === null) {
            return null;
        }

        $signature = $this->cookieSignature($workspace->uuid, $context);

        return implode('|', [self::COOKIE_VERSION, $workspace->uuid, $signature]);
    }

    private function verifiedCookieUuid(Request $request): ?string
    {
        $cookieValue = $request->cookie(self::COOKIE_NAME);

        if (! is_string($cookieValue) || $cookieValue === '') {
            return null;
        }

        $parts = explode('|', $cookieValue);

        if (count($parts) !== 3 || $parts[0] !== self::COOKIE_VERSION) {
            return null;
        }

        [$version, $uuid, $signature] = $parts;
        unset($version);

        if ($uuid === '' || $signature === '') {
            return null;
        }

        $context = $this->cookieSignatureContext($request);

        if ($context === null) {
            return null;
        }

        $expectedSignature = $this->cookieSignature($uuid, $context);

        return hash_equals($expectedSignature, $signature) ? $uuid : null;
    }

    private function cookieSignature(string $uuid, string $context): string
    {
        return hash_hmac('sha256', $uuid . '|' . $context, $this->cookieSigningKey());
    }

    private function cookieSignatureContext(Request $request): ?string
    {
        $user = $request->user();

        if ($user !== null) {
            return 'user:' . $user->getAuthIdentifier();
        }

        if (! $request->hasSession()) {
            return null;
        }

        $sessionId = $request->session()->getId();

        return $sessionId !== '' ? 'session:' . $sessionId : null;
    }

    private function cookieSigningKey(): string
    {
        $key = (string) config('app.key');

        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);

            if (is_string($decoded) && $decoded !== '') {
                return $decoded;
            }
        }

        return $key;
    }

    private function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable) {
            return false;
        }
    }
}
