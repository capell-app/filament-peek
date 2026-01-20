<?php

declare(strict_types=1);

namespace Capell\Blog\Enums;

use Capell\Blog\View\Components\Widget\Page\RelatedWidget;
use Capell\Core\Enums\Attribute\Component;
use Capell\Core\Enums\Attribute\EnumAttributeHelper;
use Capell\Core\Enums\Attribute\EnumAttributeInterface;

enum WidgetComponentEnum: string implements EnumAttributeInterface
{
    use EnumAttributeHelper;

    case Archives = 'capell-blog::widget.page.archives';
    case Article = 'capell-blog::widget.page.article';
    #[Component(RelatedWidget::class)]
    case PageRelated = 'capell-blog::widget.page.related';
    case Tags = 'capell-blog::widget.tag.tags';

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
