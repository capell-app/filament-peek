<?php

declare(strict_types=1);

namespace Capell\Layout\Actions;

use Capell\Admin\Actions\BuildDefaultTranslationsAction;
use Lorisleiva\Actions\Concerns\AsObject;

/**
 * @method static array run(array $data = [])
 */
class MutateContentDataBeforeFillAction
{
    use AsObject;

    public function handle(array $data = []): array
    {
        $data = MutateContentDataBeforeCreateAction::run($data);

        if (empty($data['translations'])) {
            $data['translations'] = BuildDefaultTranslationsAction::run($data['site_id'] ?? null);
        }

        return $data;
    }
}
