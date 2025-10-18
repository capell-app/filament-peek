<?php

declare(strict_types=1);

namespace Capell\Address\Filament\Resources\Addresses\Schemas;

use Capell\Address\Filament\Resources\Addresses\Schemas\Types\DefaultAddressSchema;
use Capell\Admin\Enums\SchemaTypeEnum;
use Capell\Admin\Facades\CapellAdmin;
use Capell\Admin\Filament\Contracts\FormConfigurator;
use Filament\Schemas\Schema;

class AddressForm implements FormConfigurator
{
    public static function configure(Schema $schema): Schema
    {
        $adminType = CapellAdmin::getSchema(SchemaTypeEnum::Address, DefaultAddressSchema::getKey());

        return $schema
            ->components(app($adminType)->make($schema))
            ->columns();
    }
}
