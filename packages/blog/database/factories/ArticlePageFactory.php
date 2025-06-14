<?php

declare(strict_types=1);

use Capell\Core\Database\Factories\PageFactory;
use Capell\Core\Models\Page;
use Capell\Core\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class ArticlePageFactory extends PageFactory
{
    public function article(?Page $parent = null): self
    {
        return $this->state(fn (): array => [
            'type_id' => Type::pageType()->firstWhere('key', 'article') ?? ArticleType::factory()->article(),
            'parent_uuid' => $parent?->getUuid(),
        ]);
    }
}
