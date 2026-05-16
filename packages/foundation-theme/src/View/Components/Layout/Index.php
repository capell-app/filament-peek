<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Layout;

use Capell\Core\Contracts\Pageable;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Site;
use Capell\Core\Models\Theme;
use Capell\FoundationTheme\Actions\BuildLayoutNeighborLinksDataAction;
use Capell\FoundationTheme\Data\LayoutNeighborLinksData;
use Capell\Frontend\Facades\Frontend;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Index extends Component
{
    public Layout $layout;

    public ?LayoutNeighborLinksData $layoutNeighborLinks;

    public Pageable $page;

    public Site $site;

    public Theme $theme;

    public bool $isSystemPageLayout;

    public function __construct()
    {
        $this->theme = Frontend::theme();
        $this->page = Frontend::page();
        $this->layout = Frontend::layout();
        $this->site = Frontend::site();
        $this->isSystemPageLayout = data_get($this->layout->admin ?? [], 'system_page_layout') === true;
        $this->layoutNeighborLinks = ! $this->isSystemPageLayout
            ? BuildLayoutNeighborLinksDataAction::run($this->page, $this->site, Frontend::language())
            : null;
    }

    public function render(): View
    {
        return view('capell::components.layout.index');
    }
}
