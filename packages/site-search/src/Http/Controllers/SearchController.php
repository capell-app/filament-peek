<?php

declare(strict_types=1);

namespace Capell\SiteSearch\Http\Controllers;

use Capell\Frontend\Facades\Frontend;
use Capell\SiteSearch\Actions\RecordSiteSearchAction;
use Capell\SiteSearch\Actions\RunSiteSearchAction;
use Capell\SiteSearch\Data\SearchRequestData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Throwable;

final class SearchController
{
    public function __invoke(Request $request): View
    {
        $query = (string) $request->query('q', '');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = (int) config('capell-site-search.results_per_page', 10);

        $site = $request->attributes->get('site');
        $language = $request->attributes->get('language');

        $data = new SearchRequestData(
            query: $query,
            page: $page,
            perPage: $perPage,
            siteId: is_object($site) ? (int) data_get($site, 'id') : null,
            languageId: is_object($language) ? (int) data_get($language, 'id') : null,
        );

        $results = RunSiteSearchAction::run($data);

        RecordSiteSearchAction::run($data, $results->total(), $request);

        $content = view('capell-site-search::pages.search', [
            'query' => $query,
            'results' => $results,
        ]);

        if (! $this->canRenderFrontendShell()) {
            return $content;
        }

        $slot = view('capell-site-search::layouts.frontend', [
            'query' => $query,
            'results' => $results,
        ]);

        return view('capell::app', [
            'language' => Frontend::language(),
            'layout' => Frontend::layout(),
            'pageRecord' => Frontend::page(),
            'site' => Frontend::site(),
            'slot' => new HtmlString($slot->render()),
            'theme' => Frontend::theme(),
        ]);
    }

    private function canRenderFrontendShell(): bool
    {
        try {
            return Frontend::language() !== null
                && Frontend::layout() !== null
                && Frontend::page() !== null
                && Frontend::site() !== null
                && Frontend::theme() !== null;
        } catch (Throwable) {
            return false;
        }
    }
}
