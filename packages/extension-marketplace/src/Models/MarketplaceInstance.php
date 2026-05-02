<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Models;

use Capell\ExtensionMarketplace\Casts\EncryptedString;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $instance_id
 * @property string $signing_secret_encrypted
 * @property CarbonImmutable|null $last_heartbeat_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class MarketplaceInstance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'extension_marketplace_instances';

    protected function casts(): array
    {
        return [
            'signing_secret_encrypted' => EncryptedString::class,
            'last_heartbeat_at' => 'datetime',
        ];
    }
}
