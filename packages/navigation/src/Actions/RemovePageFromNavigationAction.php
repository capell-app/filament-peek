<?php

declare(strict_types=1);

namespace Capell\Navigation\Actions;

use Capell\Core\Contracts\Pageable;
use Capell\Navigation\Enums\NavigationItemType;
use Capell\Navigation\Models\Navigation;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsObject;

/**
 * @method static void run(Pageable $page, Navigation $navigation)
 */
class RemovePageFromNavigationAction
{
    use AsObject;

    public function handle(Pageable $page, Navigation $navigation): void
    {
        $items = collect($navigation->items);
        $updatedItems = $this->removePageFromItems($items, $page);

        if ($updatedItems->count() !== $items->count()) {
            $navigation->update(['items' => $updatedItems->all()]);
        }
    }

    private function isPageItem(array $item, Pageable $page): bool
    {
        return ($item['type'] ?? null) === NavigationItemType::Page->value
            && (string) $item['data']['pageable_type'] === $page->getMorphClass()
            && (int) $item['data']['pageable_id'] === $page->getKey();
    }

    private function removePageFromItems(Collection $items, Pageable $page): Collection
    {
        $result = collect();
        foreach ($items as $item) {
            if ($this->isPageItem($item, $page)) {
                continue;
            }

            if (isset($item['children']) && is_array($item['children']) && $item['children'] !== []) {
                $item['children'] = $this->removePageFromItems(collect($item['children']), $page)->all();
            }

            $result->push($item);
        }

        return $result;
    }
}
