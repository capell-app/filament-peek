<?php

declare(strict_types=1);

namespace Capell\Plugins\Actions;

use Capell\Plugins\Models\MarketplacePluginLicense;
use Capell\Plugins\Services\AnystackClient;
use Lorisleiva\Actions\Action;
use Throwable;

final class DeactivateLicenseAction extends Action
{
    public function __construct(
        private readonly AnystackClient $anystackClient,
    ) {}

    public function handle(MarketplacePluginLicense $license): MarketplacePluginLicense
    {
        $plugin = $license->plugin;

        $remoteRemoved = null;
        $remoteError = null;

        if (
            $plugin !== null
            && $plugin->anystack_product_id !== null
            && $license->anystack_license_id !== null
            && $license->anystack_activation_id !== null
        ) {
            try {
                $remoteRemoved = $this->anystackClient->deactivateLicense(
                    $plugin->anystack_product_id,
                    $license->anystack_license_id,
                    $license->anystack_activation_id,
                );
            } catch (Throwable $exception) {
                // Local deletion still proceeds; surface the error in the audit log.
                $remoteError = $exception->getMessage();
            }
        }

        $license->delete();

        if ($plugin !== null) {
            $plugin->auditLog()->create([
                'action' => 'license_deactivated',
                'actor_id' => auth()->id(),
                'data' => [
                    'site_id' => $license->site_id,
                    'license_id' => $license->id,
                    'anystack_license_id' => $license->anystack_license_id,
                    'anystack_activation_id' => $license->anystack_activation_id,
                    'remote_removed' => $remoteRemoved,
                    'remote_error' => $remoteError,
                ],
                'created_at' => now(),
            ]);
        }

        return $license;
    }
}
