<?php

declare(strict_types=1);

require_once __DIR__ . '/../../scripts/audit-manifest-v3.php';

use Symfony\Component\Finder\Finder;

it('traces provider-discovered contribution types in each manifest', function (): void {
    $audit = capell_manifest_v3_audit(dirname(__DIR__, 2));
    $missing = [];

    foreach ($audit['packages'] as $slug => $package) {
        if ($package['missingContributionTypes'] !== []) {
            $missing[$slug] = $package['missingContributionTypes'];
        }
    }

    expect($missing)->toBe(
        [],
        'Provider-discovered contribution types must be declared in contributes or deferred in contributionTraceability.deferredContributions: ' .
        json_encode($missing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    );
});

it('keeps manifest contribution rows structurally valid', function (): void {
    $invalid = [];

    foreach (capell_manifest_v3_manifest_payloads(dirname(__DIR__, 2)) as $path => $manifest) {
        foreach (($manifest['contributes'] ?? []) as $index => $contribution) {
            if (! is_array($contribution)) {
                $invalid[$path][] = sprintf('contributes.%s must be an object', $index);

                continue;
            }

            foreach (['type', 'class'] as $field) {
                if (! is_string($contribution[$field] ?? null) || $contribution[$field] === '') {
                    $invalid[$path][] = sprintf('contributes.%s.%s must be a non-empty string', $index, $field);
                }
            }
        }

        foreach (capell_manifest_v3_deferred_contribution_types($manifest) as $type) {
            if (! array_key_exists($type, CAPELL_MANIFEST_V3_CONTRIBUTION_PATTERNS)) {
                $invalid[$path][] = 'contributionTraceability.deferredContributions contains unknown type ' . $type;
            }
        }
    }

    expect($invalid)->toBe(
        [],
        'Contribution rows must be typed and traceable: ' .
        json_encode($invalid, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    );
});

it('keeps package metadata out of providers', function (): void {
    $providers = (new Finder)
        ->files()
        ->in(__DIR__ . '/../../packages')
        ->path('#/src/#')
        ->name('*ServiceProvider.php');

    $metadataRegistrations = [];

    foreach ($providers as $provider) {
        if (str_contains($provider->getContents(), 'CapellCore::registerPackage(')) {
            $metadataRegistrations[] = $provider->getRelativePathname();
        }
    }

    sort($metadataRegistrations);

    expect($metadataRegistrations)->toBe(
        [],
        'Package metadata must live in capell.json manifests, not provider-side CapellCore::registerPackage() calls: ' .
        json_encode($metadataRegistrations, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    );
});
