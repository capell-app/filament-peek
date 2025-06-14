<?php

declare(strict_types=1);

namespace Capell\Layout\Models;

use Capell\Core\Concerns\HasMetaData;
use Capell\Core\Concerns\HasResources;
use Capell\Core\Contracts\CacheablePageInterface;
use Capell\Core\Database\Factories\WidgetAssetFactory;
use Capell\Core\Enums\TypeEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson;
use Wildside\Userstamps\Userstamps;

/**
 * @property int $id
 * @property int $widget_id
 * @property int|null $page_id
 * @property string|null $container
 * @property int|null $occurrence
 * @property string $asset_type
 * @property string $asset_id
 * @property int $order
 * @property array<array-key, mixed>|null $meta
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $destroyer
 * @property-read \App\Models\User|null $editor
 * @property-read Media|null $image
 * @property-read Page|null $page
 * @property-read Page|null $relatedPage
 * @property-read Model|Eloquent $asset
 * @property-read Widget $widget
 * @property-read \Illuminate\Database\Eloquent\Collection|Content[] $related
 * @property-read int|null $related_count
 *
 * @method static \Capell\Core\Database\Factories\WidgetAssetFactory factory($count = null, $state = [])
 * @method staric Builder<static>|WidgetResource newModelQuery()
 * @method static Builder<static>|WidgetAsset newQuery()
 * @method static Builder<static>|WidgetAsset ordered(string $dir = 'asc')
 * @method static Builder<static>|WidgetAsset query()
 * @method static Builder<static>|WidgetAsset withResourceables(bool $withDrafts = true)
 *
 * @mixin \Eloquent
 *
 * @property-read string $asset_key
 *
 * @method static Builder<static>|WidgetAsset newModelQuery()
 *
 * @mixin Eloquent
 */
class WidgetAsset extends Model implements CacheablePageInterface
{
    /** @use HasFactory<WidgetAssetFactory> */
    use HasFactory;

    use HasJsonRelationships;
    use HasMetaData;
    use HasPageCache;
    use HasResources;
    use Userstamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'container',
        'page_id',
        'meta',
        'occurrence',
        'order',
        'asset_id',
        'asset_type',
        'widget_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'meta' => 'json',
        'order' => 'integer',
        'occurrence' => 'integer',
    ];

    protected static string $factory = WidgetAssetFactory::class;

    public static function getTypes(): array
    {
        return TypeEnum::getResourceTypes();
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(Widget::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function asset(): MorphTo
    {
        return $this->morphTo('asset', 'asset_type', 'asset_id', 'uuid');
    }

    public function related(): BelongsToJson
    {
        return $this->belongsToJson(Content::class, 'meta->related');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'meta->image_id');
    }

    public function relatedPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'meta->related_page_id');
    }

    public function scopeOrdered(Builder $query, string $dir = 'asc'): void
    {
        $query->orderBy($this->qualifyColumn('order'), $dir);
    }

    public function getAssetKeyAttribute(): string
    {
        return $this->asset_type.'.'.$this->asset_id;
    }
}
