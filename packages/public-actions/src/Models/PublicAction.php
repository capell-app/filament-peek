<?php

declare(strict_types=1);

namespace Capell\PublicActions\Models;

use Capell\Core\Models\Site;
use Capell\PublicActions\Database\Factories\PublicActionFactory;
use Capell\PublicActions\Enums\PublicActionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

/**
 * @property PublicActionStatus $status
 * @property int|null $site_id
 * @property string $site_scope_key
 * @property string $key
 * @property string $name
 * @property string $handler_key
 * @property string|null $success_redirect_url
 * @property string|null $failure_redirect_url
 * @property string|null $success_message
 * @property string|null $failure_message
 * @property array<string, mixed>|null $payload_schema
 * @property array<string, mixed>|null $settings
 */
class PublicAction extends Model
{
    /** @use HasFactory<PublicActionFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'site_id',
        'site_scope_key',
        'key',
        'name',
        'status',
        'handler_key',
        'success_redirect_url',
        'failure_redirect_url',
        'success_message',
        'failure_message',
        'payload_schema',
        'settings',
    ];

    protected static string $factory = PublicActionFactory::class;

    #[Override]
    public function getTable(): string
    {
        $tableName = config('capell-public-actions.tables.actions');

        return is_string($tableName) ? $tableName : 'public_actions';
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(PublicActionDestination::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(PublicActionSubmission::class);
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'status' => PublicActionStatus::class,
            'payload_schema' => 'array',
            'settings' => 'array',
        ];
    }
}
