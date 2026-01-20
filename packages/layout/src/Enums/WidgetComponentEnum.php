<?php

declare(strict_types=1);

namespace Capell\Layout\Enums;

use Capell\Core\Enums\Attribute\Component;
use Capell\Core\Enums\Attribute\EnumAttributeHelper;
use Capell\Core\Enums\Attribute\EnumAttributeInterface;
use Capell\Layout\View\Components\Widget\Page\ChildrenWidget;
use Capell\Layout\View\Components\Widget\Page\LatestWidget;
use Capell\Layout\View\Components\Widget\Page\SiblingsWidget;

enum WidgetComponentEnum: string implements EnumAttributeInterface
{
    use EnumAttributeHelper;

    case AssetAccordion = 'capell-layout::widget.asset.accordion';
    case AssetBanner = 'capell-layout::widget.asset.banners';
    case AssetBlock = 'capell-layout::widget.asset.blocks';
    case AssetCarousel = 'capell-layout::widget.asset.carousel';
    case AssetFeatures = 'capell-layout::widget.asset.features';
    case AssetMedia = 'capell-layout::widget.asset.media';
    case AssetTestimonials = 'capell-layout::widget.asset.testimonials';
    case Assets = 'capell-layout::widget.asset';
    case BannerImage = 'capell-layout::widget.banner-image';
    case Default = 'capell-layout::widget.default';
    case Navigation = 'capell-layout::widget.navigation';
    case NavigationTabs = 'capell-layout::widget.navigation.tabs';
    case PageBreadcrumbs = 'capell-layout::widget.page.breadcrumbs';
    #[Component(ChildrenWidget::class)]
    case PageChildren = 'capell-layout::widget.page.children';
    case PageContent = 'capell-layout::widget.page.content';
    #[Component(LatestWidget::class)]
    case PageLatest = 'capell-layout::widget.page.latest';
    #[Component(SiblingsWidget::class)]
    case PageSiblings = 'capell-layout::widget.page.siblings';
    case PageSlot = 'capell-layout::widget.slot';
    case Pages = 'capell-layout::widget.asset.pages';

    public static function getComponents(): array
    {
        $attributes = self::getAllCaseAttributes(Component::class);

        return array_map(fn (?Component $attribute): ?string => $attribute?->class ?? null, $attributes);
    }

    public function getComponent(): ?string
    {
        return $this->getCaseAttribute(Component::class)?->class;
    }
}
