<?php

declare(strict_types=1);

namespace Capell\Layout\Enums;

enum WidgetComponentEnum: string
{
    case Default = 'capell::widget.default';

    case LivewirePages = 'capell::livewire.widget.pages';

    case Navigation = 'capell::widget.navigation';

    case Breadcrumbs = 'capell::widget.breadcrumbs';
    case PageChildren = 'capell::widget.pages.children';
    case PageContent = 'capell::widget.page.content';
    case PageLatest = 'capell::widget.pages.latest';
    case PageRelated = 'capell::widget.pages.related';
    case PageSiblings = 'capell::widget.pages.siblings';
    case PageSitemap = 'capell::widget.page.sitemap';

    case Resources = 'capell::widget.assets';
    case ResourcesAccordion = 'capell::widget.assets.accordion';
    case ResourcesMedia = 'capell::widget.assets.media';
    case ResourcesMediaCarousel = 'capell::widget.assets.media.carousel';

    case Tags = 'capell::widget.tag.tags';
}
