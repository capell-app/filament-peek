<?php

declare(strict_types=1);

namespace Capell\Tests\Assistant\Integration\Actions;

use Capell\Admin\Contracts\AiActionContextInterface;

class FakeContext implements AiActionContextInterface
{
    public function __construct(
        private readonly string $content = 'Sample',
        private readonly string $keywords = 'kw',
        private readonly int $pageId = 1,
        private readonly int $languageId = 1,
    ) {}

    public function getContent(): string
    {
        return $this->content;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }
}
