<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Block\Page;

use Capell\Core\Contracts\Pageable;
use Capell\Core\Models\Language;
use Capell\Core\Models\Site;
use Capell\FoundationTheme\View\Components\Block\AbstractBlock;
use Capell\Frontend\Facades\Frontend;
use Capell\Frontend\Support\Loader\PageLoader;
use Closure;
use Illuminate\Contracts\View\View;
use Override;
use Throwable;

class Content extends AbstractBlock
{
    public ?Pageable $nextPage = null;

    public ?Pageable $previousPage = null;

    protected static string $defaultView = 'capell-foundation-theme::components.block.page.content';

    #[Override]
    public function render(array $data = []): View|string|Closure
    {
        return parent::render([
            ...$data,
            'previousPage' => $this->previousPage,
            'nextPage' => $this->nextPage,
        ]);
    }

    protected function mountBlock(): void
    {
        try {
            $page = Frontend::page();
            $language = Frontend::language();
            $site = Frontend::site();
        } catch (Throwable) {
            return;
        }

        if (! $page instanceof Pageable || ! $language instanceof Language || ! $site instanceof Site) {
            return;
        }

        if ((bool) $page->getMeta('with_next_prev')) {
            $this->previousPage = PageLoader::getPreviousPage($page, $site, $language);
            $this->nextPage = PageLoader::getNextPage($page, $site, $language);
        }
    }
}
