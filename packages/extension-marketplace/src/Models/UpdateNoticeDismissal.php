<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $notice_id
 * @property CarbonImmutable|null $dismissed_until
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class UpdateNoticeDismissal extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'extension_marketplace_update_notice_dismissals';

    protected function casts(): array
    {
        return [
            'dismissed_until' => 'immutable_datetime',
        ];
    }
}
