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

    public function getClassName(): string
    {
        return match ($this) {
            self::ArchivePage => config('capell-blog.livewire_components.archive_page', ArchivePage::class),
            self::BlogPage => config('capell-blog.livewire_components.archive_page', BlogPage::class),
            self::TagPage => config('capell-blog.livewire_components.archive_page', TagPage::class),
        };
    }
}
