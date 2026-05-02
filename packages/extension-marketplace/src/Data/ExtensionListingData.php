<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Throwable;

final class ExtensionListingData extends Data
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly string $composerName,
        public readonly string $kind,
        public readonly ?string $description,
        public readonly int $priceCents,
        public readonly bool $isPaid,
        public readonly ?string $forkRepoUrl,
        public readonly ?string $productId,
        public readonly ?string $latestVersion = null,
        public readonly ?CarbonImmutable $releasedAt = null,
        /** @var array<string, mixed> */
        public readonly array $capabilities = [],
        public readonly ?string $capellVersionConstraint = null,
        public readonly ?string $laravelVersionConstraint = null,
        public readonly ?string $filamentVersionConstraint = null,
        public readonly ?string $documentationUrl = null,
        public readonly bool $requiresConfirmation = false,
        /** @var array<string, mixed> */
        public readonly array $installConfirmation = [],
        /** @var array<int, array<string, mixed>> */
        public readonly array $installOptions = [],
        public readonly bool $isFeatured = false,
        public readonly ?int $featuredRank = null,
        public readonly ?string $purchaseUrl = null,
        public readonly ?string $imageUrl = null,
        public readonly ?string $livewireVersionConstraint = null,
        /** @var array<int, string> */
        public readonly array $categories = [],
        public readonly bool $publisherVerified = false,
        public readonly bool $securityReviewed = false,
    ) {}

    /**
     * @param  array<string, mixed>  $item
     */
    public static function fromApiResponse(array $item): self
    {
        return new self(
            slug: (string) ($item['slug'] ?? ''),
            name: (string) ($item['name'] ?? ''),
            composerName: (string) ($item['composer_name'] ?? ''),
            kind: (string) ($item['kind'] ?? 'tool'),
            description: $item['description'] ?? null,
            priceCents: (int) ($item['price_cents'] ?? 0),
            isPaid: (bool) ($item['is_paid'] ?? false),
            forkRepoUrl: $item['fork_repo_url'] ?? null,
            productId: $item['product_id'] ?? null,
            latestVersion: isset($item['latest_version']) && is_scalar($item['latest_version']) ? (string) $item['latest_version'] : null,
            releasedAt: self::parseReleasedAt($item['released_at'] ?? null),
            capabilities: $item['capabilities'] ?? [],
            capellVersionConstraint: $item['capell_version_constraint'] ?? null,
            laravelVersionConstraint: $item['laravel_version_constraint'] ?? null,
            filamentVersionConstraint: $item['filament_version_constraint'] ?? null,
            documentationUrl: $item['documentation_url'] ?? null,
            requiresConfirmation: (bool) ($item['requires_confirmation'] ?? false),
            installConfirmation: is_array($item['install_confirmation'] ?? null) ? $item['install_confirmation'] : [],
            installOptions: is_array($item['install_options'] ?? null) ? $item['install_options'] : [],
            isFeatured: (bool) ($item['is_featured'] ?? false),
            featuredRank: isset($item['featured_rank']) && is_numeric($item['featured_rank'])
                ? (int) $item['featured_rank']
                : null,
            purchaseUrl: self::nonEmptyString($item['purchase_url'] ?? $item['checkout_url'] ?? null),
            imageUrl: self::nonEmptyString($item['image_url'] ?? $item['logo_url'] ?? $item['icon_url'] ?? null),
            livewireVersionConstraint: $item['livewire_version_constraint'] ?? null,
            categories: self::stringList($item['categories'] ?? $item['category_slugs'] ?? []),
            publisherVerified: (bool) ($item['publisher_verified'] ?? $item['is_publisher_verified'] ?? false),
            securityReviewed: (bool) ($item['security_reviewed'] ?? $item['is_security_reviewed'] ?? false),
        );
    }

    private static function parseReleasedAt(mixed $releasedAt): ?CarbonImmutable
    {
        if (! is_string($releasedAt) || $releasedAt === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($releasedAt);
        } catch (Throwable) {
            return null;
        }
    }

    private static function nonEmptyString(mixed $value): ?string
    {
        return is_string($value) && $value !== ''
            ? $value
            : null;
    }

    /** @return array<int, string> */
    private static function stringList(mixed $values): array
    {
        if (! is_array($values)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn (mixed $value): ?string => is_scalar($value) && (string) $value !== '' ? (string) $value : null,
            $values,
        ), is_string(...)));
    }
}
