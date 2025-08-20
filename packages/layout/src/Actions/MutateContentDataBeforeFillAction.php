<?php

declare(strict_types=1);

namespace Capell\Layout\Actions;

use Capell\Core\Enums\ModelEnum;
use Capell\Core\Facades\CapellCore;
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

        if (empty($data['translations']) && ! empty($data['site_id'])) {
            $data['translations'] = CapellCore::getModel(ModelEnum::SiteDomain)::query()
                ->where('site_id', $data['site_id'])
                ->pluck('language_id')
                ->map(fn ($language): array => [
                    'language_id' => $language->id,
                ])
                ->toArray();
        }

        return $data;
    }
}
