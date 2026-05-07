<?php

declare(strict_types=1);

use Capell\BlockLibrary\Actions\CreateContentAction;
use Capell\BlockLibrary\Actions\ReplicateContentAction;
use Capell\BlockLibrary\Enums\LayoutTypeEnum;
use Capell\Core\Models\Language;
use Capell\Core\Models\Type;

it('replicates content block attributes and replacement translations', function (): void {
    $language = Language::factory()->english()->create();
    $type = Type::query()->create([
        'name' => 'Content',
        'key' => 'content',
        'type' => LayoutTypeEnum::ContentBlock->value,
        'status' => true,
    ]);

    $content = CreateContentAction::run([
        'name' => 'Original content',
        'type_id' => $type->getKey(),
        'meta' => ['color' => 'primary'],
        'translations' => [
            [
                'language_id' => $language->getKey(),
                'title' => 'Original public title',
                'content' => '<p>Original public copy.</p>',
            ],
        ],
    ]);

    $replica = ReplicateContentAction::run($content, [
        'name' => 'Replicated content',
        'meta' => ['color' => 'secondary'],
        'translations' => [
            [
                'language_id' => $language->getKey(),
                'title' => 'Replicated public title',
                'content' => '<p>Replicated public copy.</p>',
            ],
        ],
    ]);

    expect($replica->getKey())->not->toBe($content->getKey())
        ->and($replica->name)->toBe('Replicated content')
        ->and($replica->type_id)->toBe($type->getKey())
        ->and($replica->meta)->toBe(['color' => 'secondary'])
        ->and($replica->translations)->toHaveCount(1)
        ->and($replica->translations->first()->title)->toBe('Replicated public title')
        ->and($content->fresh()->translations()->first()->title)->toBe('Original public title');
});
