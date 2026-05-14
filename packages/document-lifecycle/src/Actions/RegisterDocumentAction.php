<?php

declare(strict_types=1);

namespace Capell\DocumentLifecycle\Actions;

use Capell\DocumentLifecycle\Enums\DocumentStatusEnum;
use Capell\DocumentLifecycle\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class RegisterDocumentAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function handle(
        string $key,
        string $title,
        ?Model $documentable = null,
        DocumentStatusEnum $status = DocumentStatusEnum::Draft,
        array $metadata = [],
    ): Document {
        return Document::query()->updateOrCreate(
            ['key' => Str::slug($key, '_')],
            [
                'title' => $title,
                'status' => $status,
                'documentable_type' => $this->morphType($documentable),
                'documentable_id' => $documentable?->getKey(),
                'metadata' => $metadata === [] ? null : $metadata,
            ],
        );
    }

    private function morphType(?Model $model): ?string
    {
        if (! $model instanceof Model) {
            return null;
        }

        $alias = array_search($model::class, Relation::morphMap(), true);

        return is_string($alias) ? $alias : $model::class;
    }
}
