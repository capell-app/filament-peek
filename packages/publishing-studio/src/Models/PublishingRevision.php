<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Models;

use Capell\PublishingStudio\Enums\PublishingRevisionEventEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

/**
 * Immutable per-entity audit snapshot captured at publish and restore
 * boundaries. Draft saves intentionally do not create revision rows.
 *
 * @property int $id
 * @property string $uuid
 * @property string $revisionable_type
 * @property int $revisionable_id
 * @property string|null $revisionable_uuid
 * @property int|null $workspace_id
 * @property int|null $version_id
 * @property int $version
 * @property PublishingRevisionEventEnum $event_type
 * @property array<string, mixed>|null $before_payload
 * @property array<string, mixed>|null $after_payload
 * @property string|null $actor_type
 * @property int|null $actor_id
 * @property string|null $notes
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Workspace|null $workspace
 * @property-read Version|null $publishedVersion
 */
class PublishingRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'revisionable_type',
        'revisionable_id',
        'revisionable_uuid',
        'workspace_id',
        'version_id',
        'version',
        'event_type',
        'before_payload',
        'after_payload',
        'actor_type',
        'actor_id',
        'notes',
    ];

    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function publishedVersion(): BelongsTo
    {
        return $this->belongsTo(Version::class, 'version_id');
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'event_type' => PublishingRevisionEventEnum::class,
            'before_payload' => 'array',
            'after_payload' => 'array',
        ];
    }
}
