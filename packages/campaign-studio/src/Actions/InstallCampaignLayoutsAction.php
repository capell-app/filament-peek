<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Actions;

use Capell\CampaignStudio\Enums\CampaignElementComponentEnum;
use Capell\CampaignStudio\Filament\Configurators\Elements\CampaignCtaBlockElementConfigurator;
use Capell\CampaignStudio\Filament\Configurators\Elements\CampaignHeroElementConfigurator;
use Capell\CampaignStudio\Filament\Configurators\Elements\CampaignLeadFormElementConfigurator;
use Capell\CampaignStudio\Support\LayoutPresets\CampaignLayoutPreset;
use Capell\CampaignStudio\Support\LayoutPresets\LeadGenerationPreset;
use Capell\CampaignStudio\Support\LayoutPresets\ProductLaunchPreset;
use Capell\CampaignStudio\Support\LayoutPresets\WebinarPreset;
use Capell\Core\Models\Blueprint;
use Capell\Core\Models\Layout;
use Capell\LayoutBuilder\Enums\LayoutTypeEnum;
use Capell\LayoutBuilder\Filament\Configurators\Types\ElementTypeConfigurator;
use Capell\LayoutBuilder\Models\Element;
use Lorisleiva\Actions\Concerns\AsAction;

final class InstallCampaignLayoutsAction
{
    use AsAction;

    /**
     * @var array<string, array{name: string, component: CampaignElementComponentEnum, configurator: class-string, icon: string}>
     */
    private const array WIDGET_DEFINITIONS = [
        'campaign-hero' => [
            'name' => 'Campaign hero',
            'component' => CampaignElementComponentEnum::CampaignHero,
            'configurator' => CampaignHeroElementConfigurator::class,
            'icon' => 'heroicon-o-megaphone',
        ],
        'campaign-cta-block' => [
            'name' => 'Campaign CTA block',
            'component' => CampaignElementComponentEnum::CampaignCtaBlock,
            'configurator' => CampaignCtaBlockElementConfigurator::class,
            'icon' => 'heroicon-o-cursor-arrow-rays',
        ],
        'campaign-lead-form' => [
            'name' => 'Campaign lead form',
            'component' => CampaignElementComponentEnum::CampaignLeadForm,
            'configurator' => CampaignLeadFormElementConfigurator::class,
            'icon' => 'heroicon-o-clipboard-document-list',
        ],
    ];

    /**
     * @return array{created: int, updated: int, skipped: int}
     */
    public function handle(bool $force = false): array
    {
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($this->presets() as $preset) {
            $layout = Layout::query()->where('key', $preset->key())->first();

            if ($layout instanceof Layout && ! $force) {
                $result['skipped']++;

                continue;
            }

            $elements = $this->elementsForPreset($preset);

            Layout::query()->updateOrCreate(
                ['key' => $preset->key()],
                [
                    'name' => $preset->name(),
                    'group' => 'CampaignStudio',
                    'containers' => $this->containersForPreset($preset, $elements),
                    'status' => true,
                    'elements' => collect($elements)
                        ->map(fn (Element $element): string => $element->key)
                        ->values()
                        ->all(),
                ],
            );

            $layout instanceof Layout ? $result['updated']++ : $result['created']++;
        }

        return $result;
    }

    /**
     * @return array<int, CampaignLayoutPreset>
     */
    private function presets(): array
    {
        return [
            new LeadGenerationPreset,
            new ProductLaunchPreset,
            new WebinarPreset,
        ];
    }

    /**
     * @return array<string, Element>
     */
    private function elementsForPreset(CampaignLayoutPreset $preset): array
    {
        $elements = [];
        $type = $this->campaignElementType();

        foreach ($preset->elements() as $elementDefinition) {
            $elementType = $elementDefinition['type'] ?? null;
            if (! is_string($elementType)) {
                continue;
            }

            if (! isset(self::WIDGET_DEFINITIONS[$elementType])) {
                continue;
            }

            $definition = self::WIDGET_DEFINITIONS[$elementType];
            $elementKey = $preset->key() . '-' . $elementType;

            $elements[$elementType] = Element::query()->updateOrCreate(
                ['key' => $elementKey],
                [
                    'name' => $preset->name() . ' - ' . $definition['name'],
                    'blueprint_id' => $type->getKey(),
                    'meta' => [
                        'component' => $definition['component'],
                    ],
                    'admin' => [
                        'configurator' => $definition['configurator']::getKey(),
                        'icon' => $definition['icon'],
                    ],
                    'status' => true,
                ],
            );
        }

        return $elements;
    }

    /**
     * @param  array<string, Element>  $elements
     * @return array<string, array{elements: array<int, array{element_key: string, occurrence: int}>, meta: array<string, mixed>}>
     */
    private function containersForPreset(CampaignLayoutPreset $preset, array $elements): array
    {
        $containers = [];

        foreach ($preset->containers() as $containerDefinition) {
            $containerKey = $containerDefinition['key'] ?? null;
            if (! is_string($containerKey)) {
                continue;
            }

            if ($containerKey === '') {
                continue;
            }

            $containers[$containerKey] = [
                'elements' => [],
                'meta' => [
                    'container' => $containerDefinition['width'] ?? null,
                ],
            ];
        }

        foreach ($preset->elements() as $elementDefinition) {
            $containerKey = $elementDefinition['container'] ?? null;
            $elementType = $elementDefinition['type'] ?? null;
            if (! is_string($containerKey)) {
                continue;
            }

            if (! isset($containers[$containerKey])) {
                continue;
            }

            if (! is_string($elementType)) {
                continue;
            }

            if (! isset($elements[$elementType])) {
                continue;
            }

            $containers[$containerKey]['elements'][] = [
                'element_key' => $elements[$elementType]->key,
                'occurrence' => 1,
            ];
        }

        return $containers;
    }

    private function campaignElementType(): Blueprint
    {
        return Blueprint::query()->firstOrCreate(
            [
                'key' => 'campaign',
                'type' => LayoutTypeEnum::Element,
            ],
            [
                'name' => __('capell-campaign-studio::generic.campaign'),
                'group' => 'campaign-studio',
                'admin' => [
                    'type_configurator' => ElementTypeConfigurator::getKey(),
                    'icon' => 'heroicon-o-megaphone',
                ],
                'meta' => [
                    'component' => CampaignElementComponentEnum::CampaignHero,
                ],
            ],
        );
    }
}
