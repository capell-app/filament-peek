<?php

declare(strict_types=1);

namespace Capell\Blog\Enums;

use Capell\Blog\Livewire\Page\ArchivePage;
use Capell\Blog\Livewire\Page\BlogPage;
use Capell\Blog\Livewire\Page\TagPage;

enum PageComponentEnum: string
{
    case ArchivePage = 'capell-blog::livewire.page.archive';
    case BlogPage = 'capell-blog::livewire.page.blog';
    case TagPage = 'capell-blog::livewire.page.tag';

    public static function getComponents(): array
    {
        $components = [];
        foreach (self::cases() as $pageComponent) {
            $components[$pageComponent->value] = $pageComponent->getComponent();
        }

        return $components;
    }

    public function getComponent(): ?string
    {
        return match ($this) {
            self::ArchivePage => ArchivePage::class,
            self::BlogPage => BlogPage::class,
            self::TagPage => TagPage::class,
        };
    }
}
