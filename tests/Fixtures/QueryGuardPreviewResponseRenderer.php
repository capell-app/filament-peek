<?php

declare(strict_types=1);

namespace Capell\FilamentPeek\Tests\Fixtures;

use Capell\Core\Enums\FrontendRuntime;
use Capell\Core\Models\Site;
use Capell\Frontend\Contracts\FrontendContextReader;
use Capell\Frontend\Contracts\FrontendResponseRenderer;
use Capell\Frontend\Data\FrontendRenderContextData;
use Capell\Frontend\Support\Render\PublicViewQueryGuard;
use Capell\Frontend\Support\Render\RenderHookRegistry;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class QueryGuardPreviewResponseRenderer implements FrontendResponseRenderer
{
    public function runtime(): FrontendRuntime
    {
        return FrontendRuntime::Blade;
    }

    public function render(FrontendRenderContextData $context): Response
    {
        throw_unless(
            resolve(FrontendContextReader::class)->getFrontendData('test.preview.prepared') === true,
            RuntimeException::class,
            'The preview render preparation event was not dispatched.',
        );
        $site = $context->site;

        $response = resolve(PublicViewQueryGuard::class)->guard(
            $context,
            static function () use ($site): Response {
                resolve(RenderHookRegistry::class);

                if ($site instanceof Site) {
                    $site->getMeta('business_name');
                    $site->getRelation('logo');
                    $site->getRelation('logoInverted');
                    $site->getRelation('translation');
                }

                return response()->make('<main>Query-safe preview renderer reached</main>');
            },
        );

        if (! $response instanceof Response) {
            throw new RuntimeException('The guarded preview renderer did not return an HTTP response.');
        }

        return $response;
    }
}
