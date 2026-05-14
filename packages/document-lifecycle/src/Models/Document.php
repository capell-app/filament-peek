<?php

declare(strict_types=1);

namespace Capell\DocumentLifecycle\Models;

use Capell\DocumentLifecycle\Enums\DocumentStatusEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $key
 * @property string $title
 * @property DocumentStatusEnum $status
 * @property string|null $documentable_type
 * @property int|null $documentable_id
 * @property array<string, mixed>|null $metadata
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
class Document extends Model
{
    protected $table = 'document_lifecycle_documents';

    protected $fillable = [
        'key',
        'title',
        'status',
        'documentable_type',
        'documentable_id',
        'metadata',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<DocumentPublication, $this>
     */
    public function publications(): HasMany
    {
        return $this->hasMany(DocumentPublication::class);
    }

    public function latestPublication(): ?DocumentPublication
    {
        return $this->publications()
            ->latest('published_at')
            ->latest('id')
            ->first();
    }

    protected function casts(): array
    {
        return [
            'status' => DocumentStatusEnum::class,
            'metadata' => 'array',
        ];
    }
}
