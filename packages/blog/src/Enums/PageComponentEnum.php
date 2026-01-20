<?php

declare(strict_types=1);

namespace Capell\Blog\Enums;

use Capell\Blog\Livewire\Page\ArchivePage;
use Capell\Blog\Livewire\Page\BlogPage;
use Capell\Blog\Livewire\Page\TagPage;
use Capell\Core\Enums\Attribute\Component;
use Capell\Core\Enums\Attribute\EnumAttributeHelper;
use Capell\Core\Enums\Attribute\EnumAttributeInterface;

enum PageComponentEnum: string implements EnumAttributeInterface
{
    use EnumAttributeHelper;

    #[Component(ArchivePage::class)]
    case ArchivePage = 'capell-blog::livewire.page.archive';

    #[Component(BlogPage::class)]
    case BlogPage = 'capell-blog::livewire.page.blog';

    #[Component(TagPage::class)]
    case TagPage = 'capell-blog::livewire.page.tag';

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
