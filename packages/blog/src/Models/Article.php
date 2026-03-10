<?php

declare(strict_types=1);

namespace Capell\Blog\Models;

use Bkwld\Cloner\Cloneable;
use Capell\Blog\Database\Factories\ArticleFactory;
use Capell\Blog\Models\Concerns\HasTags;
use Capell\Blog\Observers\ArticleObserver;
use Capell\Core\Contracts\Pageable;
use Capell\Core\Contracts\PageCacheable;
use Capell\Core\Enums\MediaCollectionEnum;
use Capell\Core\Enums\PublishStatusEnum;
use Capell\Core\Models\AssetRelation;
use Capell\Core\Models\Concerns\CloneableExcept;
use Capell\Core\Models\Concerns\HasAssets;
use Capell\Core\Models\Concerns\HasDrafts;
use Capell\Core\Models\Concerns\HasMetaData;
use Capell\Core\Models\Concerns\HasMorphModelRelations;
use Capell\Core\Models\Concerns\HasPublishDates;
use Capell\Core\Models\Concerns\HasTranslations;
use Capell\Core\Models\Concerns\HasType;
use Capell\Core\Models\Concerns\HasTypes;
use Capell\Core\Models\Concerns\HasUserstamps;
use Capell\Core\Models\Concerns\InteractsWithMedia;
use Capell\Core\Models\Contracts\Draftable;
use Capell\Core\Models\Contracts\Publishable;
use Capell\Core\Models\Contracts\Translatable;
use Capell\Core\Models\Contracts\Typeable;
use Capell\Core\Models\Contracts\Userstampable;
use Capell\Core\Models\Language;
use Capell\Core\Models\Layout;
use Capell\Core\Models\PageUrl;
use Capell\Core\Models\Site;
use Capell\Core\Models\Translation;
use Capell\Core\Models\Type;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Kalnoy\Nestedset\QueryBuilder as NestedQueryBuilder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson;

/**
 * @property int $id
 * @property string $name
 * @property int $type_id
 * @property int $layout_id
 * @property int $site_id
 * @property int|null $parent_id
 * @property array<array-key, mixed>|null $meta
 * @property CarbonImmutable|null $publish_from
 * @property CarbonImmutable|null $publish_to
 * @property string|null $uuid
 * @property CarbonImmutable|null $published_at
 * @property bool $is_published
 * @property bool $is_current
 * @property string|null $publisher_type
 * @property int|null $publisher_id
 * @property int $order
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read Collection<int, AssetRelation> $assetRelations
 * @property-read int|null $asset_relations_count
 * @property-read Collection<int, AssetRelation> $assets
 * @property-read int|null $assets_count
 * @property-read Article|null $parent
 * @property-read Collection<int, Article> $children
 * @property-read int|null $children_count
 * @property-read User|null $creator
 * @property-read User|null $destroyer
 * @property-read User|null $editor
 * @property-read Collection<int, Article> $drafts
 * @property-read int|null $drafts_count
 * @property-read static|null $draft
 * @property-read PublishStatusEnum $publish_status
 * @property-read string|null $title
 * @property-read Article|null $hasDraftsAndNestedSetParent
 * @property-read Media|null $image
 * @property-read Collection<int, Language> $languages
 * @property-read int|null $languages_count
 * @property-read Layout $layout
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Article|null $nodeTraitParent
 * @property-read PageUrl $pageUrl
 * @property-read Collection<int, PageUrl> $pageUrls
 * @property-read int|null $page_urls_count
 * @property-read Model|null $publisher
 * @property-read Collection<int, Article> $revisions
 * @property-read int|null $revisions_count
 * @property-read Collection<int, Article> $siblings
 * @property-read int|null $siblings_count
 * @property-read Site $site
 * @property-read Translation|null $translation
 * @property-read Collection<int, Translation> $translations
 * @property-read int|null $translations_count
 * @property-read Type $type
 * @property-read Collection<int, Article> $related
 * @property-read int|null $related_count
 *
 * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
 * @method static NestedQueryBuilder<static>|Article ancestorsAndSelf($id, array $columns = [])
 * @method static NestedQueryBuilder<static>|Article ancestorsOf($id, array $columns = [])
 * @method static NestedQueryBuilder<static>|Article applyNestedSetScope(?string $table = null)
 * @method static NestedQueryBuilder<static>|Article countErrors()
 * @method static NestedQueryBuilder<static>|Article current()
 * @method static NestedQueryBuilder<static>|Article d()
 * @method static NestedQueryBuilder<static>|Article defaultOrder(string $dir = 'asc')
 * @method static NestedQueryBuilder<static>|Article descendantsAndSelf($id, array $columns = [])
 * @method static NestedQueryBuilder<static>|Article descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static NestedQueryBuilder<static>|Article excludeRevision(Model|int $exclude)
 * @method static NestedQueryBuilder<static>|Article expired(Model $model)
 * @method static ArticleFactory factory($count = null, $state = [])
 * @method static NestedQueryBuilder<static>|Article fixSubtree($root)
 * @method static NestedQueryBuilder<static>|Article fixTree($root = null)
 * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
 * @method static NestedQueryBuilder<static>|Article hasChildren()
 * @method static NestedQueryBuilder<static>|Article hasParent()
 * @method static NestedQueryBuilder<static>|Article isBroken()
 * @method static NestedQueryBuilder<static>|Article leaves(array $columns = [])
 * @method static NestedQueryBuilder<static>|Article newModelQuery()
 * @method static NestedQueryBuilder<static>|Article newQuery()
 * @method static Builder<static>|Article onlyTrashed()
 * @method static NestedQueryBuilder<static>|Article ordered(string $dir = 'asc')
 * @method static NestedQueryBuilder<static>|Article pending(Model $model)
 * @method static NestedQueryBuilder<static>|Article published(Model $model)
 * @method static NestedQueryBuilder<static>|Article query()
 * @method static NestedQueryBuilder<static>|Article root(array $columns = [])
 * @method static Builder<static>|Article withTrashed()
 * @method static NestedQueryBuilder<static>|Article withWhereHasLanguage(int $language_id)
 * @method static NestedQueryBuilder<static>|Article withoutCurrent()
 * @method static NestedQueryBuilder<static>|Article withoutRoot()
 * @method static NestedQueryBuilder<static>|Article withoutSelf()
 * @method static Builder<static>|Article withoutTrashed()
 *
 * @mixin Model
 */
#[ObservedBy(ArticleObserver::class)]
class Article extends Model implements Draftable, HasMedia, Pageable, PageCacheable, Publishable, Translatable, Typeable, Userstampable
{
    use Cloneable;
    use CloneableExcept;
    use HasAssets;
    use HasDrafts;

    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    use HasJsonRelationships;
    use HasMetaData;
    use HasMorphModelRelations;
    use HasPublishDates;
    use HasTags;
    use HasTranslations;
    use HasType;
    use HasTypes;
    use HasUserstamps;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'articles';

    /**
     * @var array<string>
     */
    protected $fillable = [
        'is_published',
        'layout_id',
        'meta',
        'name',
        'order',
        'publish_from',
        'publish_to',
        'published_at',
        'site_id',
        'type_id',
        'uuid',
    ];

    protected array $clone_exempt_attributes = [
        'hidden',
    ];

    protected array $draftableRelations = [
        'translations',
    ];

    protected static string $factory = ArticleFactory::class;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('article')
            ->logAll()
            ->logExcept([
                'updated_at',
                'created_at',
                'deleted_at',
                'draft_id',
                'is_published',
                'is_current',
                'publisher_type',
                'publisher_id',
                'created_by',
                'updated_by',
                'deleted_by',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionEnum::Image->value)->singleFile();
    }

    public function getPublishDate(): ?CarbonImmutable
    {
        $date = $this->publish_from ?? $this->created_at;

        return $date !== null ? CarbonImmutable::make($date) : null;
    }

    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /** @return MorphOne<PageUrl, self> */
    public function pageUrl(): MorphOne
    {
        return $this->morphOne(PageUrl::class, 'pageable')->withDefault(['site_id' => $this->site_id]);
    }

    /** @return MorphMany<PageUrl, self> */
    public function pageUrls(): MorphMany
    {
        $model = $this->morphMany(PageUrl::class, 'pageable');

        if (method_exists($model, 'chaperone')) {
            $model->chaperone('article');
        }

        return $model;
    }

    public function image(): MorphOne
    {
        return $this->morphOneMedia(MediaCollectionEnum::Image->value);
    }

    /**
     * @return BelongsToJson<Article, self>
     */
    public function related(): BelongsToJson
    {
        return $this->belongsToJson(self::class, 'meta->related');
    }

    protected function scopeOrdered(Builder $query, string $dir = 'asc'): void
    {
        $query->orderBy($this->qualifyColumn('order'), $dir);
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'meta' => 'json',
            'publish_from' => 'datetime',
            'publish_to' => 'datetime',
        ];
    }
}
