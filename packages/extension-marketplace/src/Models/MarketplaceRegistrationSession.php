<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Models;

use Capell\ExtensionMarketplace\Enums\MarketplaceRegistrationStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $marketplace_registration_id
 * @property string $domain
 * @property string $challenge_id
 * @property string $challenge_token
 * @property string|null $verification_url
 * @property MarketplaceRegistrationStatus $status
 * @property CarbonImmutable|null $expires_at
 * @property CarbonImmutable|null $verified_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class MarketplaceRegistrationSession extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'extension_marketplace_registration_sessions';

    protected function casts(): array
    {
        return [
            'status' => MarketplaceRegistrationStatus::class,
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }
}
