<?php

declare(strict_types=1);

use Capell\Blog\Models\Tag;
use Capell\Mosaic\Models\Section;

it('can be attached to sections', function (): void {
    $tag = Tag::factory()->create();
    $section = Section::factory()->create();

    $tag->sections()->attach($section);

    expect($tag->sections)->toHaveCount(1)
        ->and($tag->sections->first()->id)->toBe($section->id);
});
