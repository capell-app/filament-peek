<?php

declare(strict_types=1);

namespace Capell\AccessGate\Tests\Support;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class FakePageCacheMiddleware
{
    public static bool $ran = false;

    public function handle(Request $request, Closure $next): Response
    {
        self::$ran = true;

        return response('cached secret');
    }
}
