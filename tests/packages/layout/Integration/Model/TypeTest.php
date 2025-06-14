<?php

declare(strict_types=1);

use Capell\Core\Enums\TypeEnum;
use Capell\Core\Models;

it('has many contents', function (): void {
    $type = Models\Type::factory()->content()->create();

    Models\Content::factory()->create(['type_id' => $type->id]);

    expect($type->refresh())
        ->contents->toHaveCount(1);
});

it('has many widgets', function (): void {
    $type = Models\Type::factory()->widget()->create();

    Models\Widget::factory()->create(['type_id' => $type->id]);

    expect($type->refresh())
        ->widgets->toHaveCount(1);
});

it('can scope content type', function (): void {
    Models\Type::factory()->create(['type' => TypeEnum::Content]);
    Models\Type::factory()->create(['type' => TypeEnum::Page]);

    $result = Models\Type::contentType()->get();

    expect($result)->toHaveCount(1);
});

it('can scope widget type', function (): void {
    Models\Type::factory()->create(['type' => TypeEnum::Widget]);
    Models\Type::factory()->create(['type' => TypeEnum::Content]);

    $result = Models\Type::widgetType()->get();

    expect($result)->toHaveCount(1);
});
