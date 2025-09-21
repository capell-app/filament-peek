<?php

declare(strict_types=1);

namespace Capell\Blog\Filament\Components\Forms\Page;

use Capell\Admin\Filament\Components\Forms\TagsInput;
use Capell\Core\Enums\TagTypeEnum;
use Capell\Core\Models\Page;

class PageTagsInput extends TagsInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->type(TagTypeEnum::PAGE->value)
            ->visible(
                fn (string $operation, ?Page $record): bool => in_array($operation, ['edit', 'editOption'], true)
                    && ($record?->type->admin['with_tags'] ?? false)
            );
    }
}
