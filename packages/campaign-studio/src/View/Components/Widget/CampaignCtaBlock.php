<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\View\Components\Widget;

use Capell\CampaignStudio\Models\CampaignCtaBlock as CampaignCtaBlockModel;
use Capell\Core\Models\Widget;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use stdClass;

class CampaignCtaBlock extends Component
{
    public ?CampaignCtaBlockModel $ctaBlock = null;

    public function __construct(
        public array $container,
        public string $containerKey,
        public int $widgetIndex,
        public stdClass $loop,
        public Widget $widget,
        public array $widgetData = [],
        public ?int $containerIndex = null,
        public ?int $containerWidth = null,
        public ?int $containerColspan = null,
        public mixed $pageSlot = null,
        public int $occurrence = 1,
    ) {
        $this->ctaBlock = $this->widget->relationLoaded('campaignCtaBlock')
            ? $this->widget->getRelation('campaignCtaBlock')
            : null;
    }

    /**
     * @param  Collection<int, Widget>  $widgets
     */
    public static function hydrateWidgets(Collection $widgets): void
    {
        $ctaBlockIds = $widgets
            ->map(fn (Widget $widget): mixed => $widget->getMeta('cta_block_id'))
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        if ($ctaBlockIds->isEmpty()) {
            return;
        }

        $ctaBlocks = CampaignCtaBlockModel::query()
            ->whereKey($ctaBlockIds->all())
            ->get()
            ->keyBy(fn (CampaignCtaBlockModel $ctaBlock): int => (int) $ctaBlock->getKey());

        $widgets->each(function (Widget $widget) use ($ctaBlocks): void {
            $ctaBlockId = $widget->getMeta('cta_block_id');

            $widget->setRelation(
                'campaignCtaBlock',
                is_numeric($ctaBlockId) ? $ctaBlocks->get((int) $ctaBlockId) : null,
            );
        });
    }

    public function render(array $data = []): View|string|Closure
    {
        return view('capell-campaign-studio::components.widget.campaign-cta-block', [
            ...$data,
            'ctaBlock' => $this->ctaBlock,
        ]);
    }
}
