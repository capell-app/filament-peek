<?php

declare(strict_types=1);

namespace Capell\Layout\Models;

use Capell\Core\Concerns\HasResources;
use Capell\Core\Contracts\CacheablePageInterface;
use Capell\Core\Database\Factories\ContentAssetFactory;
use Capell\Core\Enums\TypeEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Wildside\Userstamps\Userstamps;

/**
 * @property int $id
 * @property int $content_id
 * @property int $order
 * @property string $asset_type
 * @property int $asset_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Content $content
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $destroyer
 * @property-read \App\Models\User|null $editor
 * @property-read Model|Eloquent $asset
 *
 * @method static \Capell\Core\Database\Factories\ContentAssetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentAsset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentAsset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentAsset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentAsset withResourceables(bool $withDrafts = true)
 *
 * @mixin \Eloquent
 *
 * @property-read string $asset_key
 *
 * @mixin Eloquent
 */
class ContentAsset extends Model implements CacheablePageInterface
{
    /** @use HasFactory<ContentAssetFactory> */
    use HasFactory;

    use HasPageCache;
    use HasResources;
    use Userstamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'content_id',
        'order',
        'asset_id',
        'asset_type',
    ];

    protected static string $factory = ContentAssetFactory::class;

    public static function getTypes(): array
    {
        return TypeEnum::getResourceTypes();
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function asset(): MorphTo
    {
        return $this->morphTo('asset', 'asset_type', 'asset_id', 'uuid');
    }

    public function getAssetKeyAttribute(): string
    {
        return $this->asset_type.'.'.$this->asset_id;
    }
}
