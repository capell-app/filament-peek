<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\View\Components\Element;

use Capell\CampaignStudio\Models\CampaignCtaBlock as CampaignCtaBlockModel;
use Capell\LayoutBuilder\Models\Element;
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
        public int $elementIndex,
        public stdClass $loop,
        public Element $element,
        public array $elementData = [],
        public ?int $containerIndex = null,
        public ?int $containerWidth = null,
        public ?int $containerColspan = null,
        public mixed $pageSlot = null,
        public int $occurrence = 1,
    ) {
        $this->ctaBlock = $this->element->relationLoaded('campaignCtaBlock')
            ? $this->element->getRelation('campaignCtaBlock')
            : null;
    }

    /**
     * @param  Collection<int, Element>  $elements
     */
    public static function hydrateElements(Collection $elements): void
    {
        $ctaBlockIds = $elements
            ->map(fn (Element $element): mixed => $element->getMeta('cta_block_id'))
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

        $elements->each(function (Element $element) use ($ctaBlocks): void {
            $ctaBlockId = $element->getMeta('cta_block_id');

            $element->setRelation(
                'campaignCtaBlock',
                is_numeric($ctaBlockId) ? $ctaBlocks->get((int) $ctaBlockId) : null,
            );
        });
    }

    public function render(array $data = []): View|string|Closure
    {
        return view('capell-campaign-studio::components.element.campaign-cta-block', [
            ...$data,
            'ctaBlock' => $this->ctaBlock,
        ]);
    }
}
