<?php

declare(strict_types=1);

namespace Capell\Plugins\Actions;

use Capell\Plugins\Capabilities\CapabilityRegistry;
use Capell\Plugins\Data\CapabilityWarningData;
use Capell\Plugins\Enums\CapabilityWarningLevel;
use Capell\Plugins\Models\MarketplacePlugin;
use Capell\Plugins\Services\AnystackClient;
use Capell\Plugins\Services\ComposerRunner;
use Lorisleiva\Actions\Action;
use RuntimeException;

final class InstallPluginAction extends Action
{
    private const STDERR_TAIL_LENGTH = 400;

    public function __construct(
        private readonly ComposerRunner $composerRunner,
        private readonly AnystackClient $anystackClient,
    ) {}

    public function handle(
        MarketplacePlugin $plugin,
        ?string $licenseKey = null,
        ?string $fingerprint = null,
    ): void {
        $isPaid = $plugin->price_once !== null
            || $plugin->price_monthly !== null
            || $plugin->price_yearly !== null;

        if ($isPaid && $licenseKey === null) {
            throw new RuntimeException(
                'Cannot install paid plugin without license key',
            );
        }

        if ($licenseKey !== null) {
            if ($plugin->anystack_product_id === null) {
                throw new RuntimeException(
                    'Cannot configure Anystack repository: plugin has no anystack_product_id configured',
                );
            }

            $repoUrl = $this->anystackClient->composerRepositoryUrl($plugin->anystack_product_id);

            $configResult = $this->composerRunner->configureAnystackRepo(
                $plugin->anystack_product_id,
                $licenseKey,
                $fingerprint,
            );

            if (! $configResult->successful()) {
                throw new RuntimeException(
                    "Failed to configure Anystack repository ({$repoUrl}): {$configResult->stderr}",
                );
            }
        }

        $installResult = $this->composerRunner->requirePackage(
            $plugin->composer_name,
            $plugin->latest_version,
        );

        if ($installResult->successful()) {
            $plugin->auditLog()->create([
                'action' => 'installed',
                'actor_id' => auth()->id(),
                'data' => [
                    'version' => $plugin->latest_version,
                ],
                'created_at' => now(),
            ]);

            return;
        }

        $stderrTail = substr($installResult->stderr, -self::STDERR_TAIL_LENGTH);

        $plugin->auditLog()->create([
            'action' => 'install_failed',
            'actor_id' => auth()->id(),
            'data' => [
                'version' => $plugin->latest_version,
                'exit_code' => $installResult->exitCode,
                'stderr_tail' => $stderrTail,
            ],
            'created_at' => now(),
        ]);

        throw new RuntimeException(
            "Plugin installation failed with exit code {$installResult->exitCode}: {$stderrTail}",
        );
    }

    public function previewCapabilityWarnings(MarketplacePlugin $plugin): CapabilityWarningData
    {
        if ($plugin->capabilities === null || count($plugin->capabilities) === 0) {
            return new CapabilityWarningData(
                highestLevel: CapabilityWarningLevel::Green,
                warnings: [],
            );
        }

        $descriptors = [];
        $warnings = [];

        foreach ($plugin->capabilities as $capabilityString) {
            try {
                $descriptor = CapabilityRegistry::parse($capabilityString);
                $descriptors[] = $descriptor;
                $warningLevelLetter = $this->getWarningLevelLetter($descriptor->warningLevel);
                $warnings[] = $warningLevelLetter . '. ' . $descriptor->title;
            } catch (RuntimeException) {
                continue;
            }
        }

        $highestLevel = CapabilityWarningLevel::Green;
        foreach ($descriptors as $descriptor) {
            if ($descriptor->warningLevel === CapabilityWarningLevel::Red) {
                $highestLevel = CapabilityWarningLevel::Red;
                break;
            }

            if ($descriptor->warningLevel === CapabilityWarningLevel::Yellow
                && $highestLevel !== CapabilityWarningLevel::Red) {
                $highestLevel = CapabilityWarningLevel::Yellow;
            }
        }

        return new CapabilityWarningData(
            highestLevel: $highestLevel,
            warnings: $warnings,
        );
    }

    private function getWarningLevelLetter(CapabilityWarningLevel $level): string
    {
        return match ($level) {
            CapabilityWarningLevel::Red => 'R',
            CapabilityWarningLevel::Yellow => 'Y',
            CapabilityWarningLevel::Green => 'G',
        };
    }
}
