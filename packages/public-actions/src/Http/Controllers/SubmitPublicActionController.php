<?php

declare(strict_types=1);

namespace Capell\PublicActions\Http\Controllers;

use Capell\PublicActions\Actions\SubmitPublicActionAction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SubmitPublicActionController
{
    public function __construct(
        private readonly SubmitPublicActionAction $submitPublicAction,
    ) {}

    public function __invoke(Request $request, string $action): Response
    {
        $result = $this->submitPublicAction->handle($action, $request->except(['_token']), $request);

        if ($request->expectsJson()) {
            return $this->noStore(response()->json([
                'success' => $result->success,
                'message' => $result->message ?? __('capell-public-actions::generic.submitted'),
                'redirect_url' => $result->redirectUrl,
            ], $result->success ? 200 : 422));
        }

        $redirectUrl = $result->redirectUrl ?? url()->previous();

        return $this->noStore(
            redirect($redirectUrl)
                ->with('public_action_status', $result->message ?? __('capell-public-actions::generic.submitted')),
        );
    }

    /**
     * @template TResponse of Response
     *
     * @param  TResponse  $response
     * @return TResponse
     */
    private function noStore(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
