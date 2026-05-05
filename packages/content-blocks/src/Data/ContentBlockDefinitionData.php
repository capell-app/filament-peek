<?php

declare(strict_types=1);

namespace Capell\ContentBlocks\Data;

use BackedEnum;
use Capell\Admin\Contracts\ConfiguratorTypeEnumInterface;
use Capell\Admin\Filament\Contracts\FormConfigurator;
use Capell\ContentBlocks\Enums\ConfiguratorTypeEnum;

class ContentBlockDefinitionData
{
    /**
     * @param  class-string<FormConfigurator>  $configurator
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $description,
        public string|BackedEnum $icon,
        public string $group,
        public string $configurator,
        public string $component,
        public array $defaults = [],
        public ConfiguratorTypeEnumInterface $configuratorType = ConfiguratorTypeEnum::ContentBlock,
    ) {}
}
