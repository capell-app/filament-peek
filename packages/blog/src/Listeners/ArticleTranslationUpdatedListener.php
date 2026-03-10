<?php

declare(strict_types=1);

namespace Capell\Blog\Listeners;

use Capell\Blog\Models\Article;
use Capell\Blog\Support\Loader\BlogLoader;
use Capell\Core\Actions\UpdatePageUrlAction;
use Capell\Core\Models\Translation;

final class ArticleTranslationUpdatedListener
{
    public function __invoke(Translation $translation): void
    {
        if ($translation->translatable_type !== app(Article::class)->getMorphClass()) {
            return;
        }

        /** @var Article $article */
        $article = $translation->translatable;

        $url = BlogLoader::getBlogPageUrl($article->site, $translation->language);

        UpdatePageUrlAction::run($article->site, $translation, $url);
    }
}
