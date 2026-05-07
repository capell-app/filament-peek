<?php

declare(strict_types=1);

namespace Capell\Events\Actions;

use Capell\Core\Models\Site;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static void run()
 */
class InstallPackageAction
{
    use AsAction;

    public function handle(): void
    {
        EnsureEventPublishingDefaultsAction::run();

        Site::with('languages')->each(function (Site $site): void {
            EnsureEventPublishingSurfaceAction::run($site);
        });
    }
}
