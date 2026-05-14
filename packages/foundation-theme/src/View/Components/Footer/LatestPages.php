<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Footer;

use Capell\Core\Enums\PageOrderEnum;
use Capell\Core\Enums\TypeGroupEnum;
use Capell\Frontend\Facades\Frontend;
use Capell\Frontend\Support\Loader\PageLoader;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class LatestPages extends Component
{
    /** @var Collection<int, mixed> */
    public Collection $pages;

    /**
     * @param  Collection<int, mixed>|null  $pages
     */
    public function __construct(public string $headingClass, public int $limit = 4, ?Collection $pages = null)
    {
        $this->pages = $pages ?? PageLoader::getPages(
            language: Frontend::language(),
            site: Frontend::site(),
            limit: $this->limit,
            ordering: PageOrderEnum::Latest,
            pageGroup: TypeGroupEnum::Default,
        );
    }

    public function hasPages(): bool
    {
        return $this->pages->isNotEmpty();
    }

    public function render(): ViewContract
    {
        return view('capell::components.footer.latest-pages');
    }
}
