<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Data;

use Spatie\LaravelData\Data;

class AiDiscoveryPageEntryData extends Data
{
    public function __construct(
        public string $title,
        public string $url,
        public ?string $markdownUrl = null,
        public ?string $description = null,
        public string $section = 'Pages',
        public int $priority = 500,
        public ?string $markdown = null,
        public ?int $pageId = null,
    ) {}

    public function toLlmsTxtLine(): string
    {
        $line = sprintf('- [%s](%s)', trim(strip_tags($this->title)), $this->markdownUrl ?? $this->url);
        $description = trim(strip_tags((string) $this->description));

        if ($description === '') {
            return $line;
        }

        return $line . ': ' . $description;
    }
}
