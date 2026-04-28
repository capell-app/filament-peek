<?php

declare(strict_types=1);

use Capell\Admin\Console\Commands\DemoCommand;
use Capell\Admin\Console\Commands\InstallCommand;
use Capell\Admin\Filament\Pages\SystemHealthPage;
use Capell\Blog\Providers\FrontendServiceProvider;
use Capell\Blog\Support\Sitemap\ArchivesSitemap;
use Capell\Blog\Support\Sitemap\ArticlesSitemap;
use Capell\Blog\Support\Sitemap\TagsSitemap;
use Capell\Core\Database\Factories\TypeFactory;
use Capell\Core\Support\Creator\DemoCreator;
use Capell\Workspaces\Providers\WorkspacesServiceProvider;
use Capell\Workspaces\Tests\WorkspacesTestCase;

arch()
    ->expect('Capell\Blog')
    ->toOnlyBeUsedIn('Capell\Blog')
    ->ignoring([
        InstallCommand::class,
        DemoCommand::class,
        DemoCreator::class,
        TypeFactory::class,
        Capell\Mosaic\Console\Commands\Hero\DemoCommand::class,
        SystemHealthPage::class,
        WorkspacesServiceProvider::class,
        WorkspacesTestCase::class,
    ]);

arch()
    ->expect('Capell\Blog')
    ->classes()
    ->toUseStrictEquality();

arch('blog package does not depend on seo-tools outside of sitemap bridges')
    ->expect('Capell\Blog')
    ->not->toUse('Capell\SeoTools')
    ->ignoring([
        ArchivesSitemap::class,
        ArticlesSitemap::class,
        TagsSitemap::class,
        FrontendServiceProvider::class,
    ]);
