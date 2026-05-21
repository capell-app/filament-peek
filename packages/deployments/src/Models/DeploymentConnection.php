<?php

declare(strict_types=1);

namespace Capell\Deployments\Models;

use Capell\Deployments\Casts\EncryptedString;
use Capell\Deployments\Database\Factories\DeploymentConnectionFactory;
use Capell\Deployments\Enums\GitProviderType;
use Capell\Deployments\Enums\InstallPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property int $id
 * @property GitProviderType $provider
 * @property string $repo_owner
 * @property string $repo_name
 * @property string $default_branch
 * @property string $access_token_encrypted
 * @property string|null $refresh_token_encrypted
 * @property CarbonImmutable|null $token_expires_at
 * @property InstallPolicy $install_policy
 * @property array<string, mixed>|null $metadata
 * @property bool $is_active
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class DeploymentConnection extends Model
{
    /** @use HasFactory<DeploymentConnectionFactory> */
    use HasFactory;

    /**
     * Explicit fillable list — encrypted token columns are deliberately omitted
     * so they can never be set via mass-assignment from Filament forms or
     * untrusted request input. Tokens must be written via forceFill in actions.
     *
     * @var list<string>
     */
    protected $fillable = [
        'provider',
        'repo_owner',
        'repo_name',
        'default_branch',
        'token_expires_at',
        'install_policy',
        'metadata',
        'is_active',
    ];

    public function repoCoordinate(): string
    {
        return sprintf('%s/%s', $this->repo_owner, $this->repo_name);
    }

    protected static function newFactory(): DeploymentConnectionFactory
    {
        return DeploymentConnectionFactory::new();
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'provider' => GitProviderType::class,
            'install_policy' => InstallPolicy::class,
            'access_token_encrypted' => EncryptedString::class,
            'refresh_token_encrypted' => EncryptedString::class,
            'token_expires_at' => 'datetime',
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }
}
