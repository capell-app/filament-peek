<?php

declare(strict_types=1);

namespace Capell\Tags\Support;

use Capell\Core\Facades\CapellCore;
use Capell\Tags\Enums\ModelEnum;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class TagModelRegistrar
{
    public static function register(): void
    {
        CapellCore::registerModels(ModelEnum::cases());

        Relation::morphMap(
            collect(ModelEnum::cases())
                ->mapWithKeys(fn (ModelEnum $model): array => [Str::snake($model->name) => $model->value])
                ->all(),
        );
    }
}
