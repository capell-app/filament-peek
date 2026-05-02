<?php

declare(strict_types=1);

namespace Capell\Redirects\Actions;

use Capell\Core\Models\PageUrl;
use Capell\Redirects\Models\RedirectHealthSnapshot;
use Lorisleiva\Actions\Concerns\AsAction;

class RefreshRedirectHealthSnapshotAction
{
    use AsAction;

    public function handle(PageUrl $redirect): RedirectHealthSnapshot
    {
        $errors = [];
        $warnings = [];

        if ($redirect->target_url !== null && $redirect->target_url !== '') {
            $result = ValidateRedirectAction::run(
                sourceUrl: $redirect->url,
                targetUrl: (string) $redirect->target_url,
                siteId: (int) $redirect->site_id,
                languageId: (int) $redirect->language_id,
                excludeId: (int) $redirect->id,
                statusCode: $redirect->status_code?->value,
                validateDuplicateSource: false,
            );

            $errors = $result['errors'];
            $warnings = $result['warnings'];
        }

        return RedirectHealthSnapshot::query()->updateOrCreate(
            ['page_url_id' => $redirect->id],
            [
                'source_url' => $redirect->url,
                'target_url' => $redirect->target_url,
                'has_chain' => $warnings !== [],
                'has_loop' => in_array(__('redirects::message.redirect_loop_detected'), $errors, true),
                'warning_count' => count($warnings),
                'error_count' => count($errors),
                'computed_at' => now(),
            ],
        );
    }
}
