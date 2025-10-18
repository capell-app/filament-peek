<?php

declare(strict_types=1);

namespace Capell\Address\Filament\Resources\Addresses\Schemas\Types;

use Capell\Address\Filament\Components\Forms\CountrySelect;
use Capell\Admin\Contracts\TypeSchemaInterface;
use Capell\Admin\Enums\SchemaExtenderEnum;
use Capell\Admin\Enums\SchemaTypeEnum;
use Capell\Admin\Filament\Components\Forms\DefaultToggle;
use Capell\Admin\Filament\Components\Forms\StatusToggle;
use Capell\Admin\Filament\Concerns\HasTypeSchema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class DefaultAddressSchema implements TypeSchemaInterface
{
    use HasTypeSchema;

    public static string $schemaType = SchemaTypeEnum::Address->value;

    public static function getExtenders(): iterable
    {
        return app()->tagged(SchemaExtenderEnum::Address->value);
    }

    public function make(Schema $schema): array
    {
        return $this->getFormSchema($schema);
    }

    private function getFormSchema(Schema $schema): array
    {
        return [
            TextInput::make('name')
                ->label(__('capell-address::form.name'))
                ->required()
                ->columnSpanFull(),
            TextInput::make('line1')
                ->label(__('capell-address::form.address_line_1'))
                ->required(),
            TextInput::make('line2')
                ->label(__('capell-address::form.address_line_2')),
            TextInput::make('city')
                ->label(__('capell-address::form.city'))
                ->required(),
            TextInput::make('state')
                ->label(__('capell-address::form.state'))
                ->required(),
            TextInput::make('postal_code')
                ->label(__('capell-address::form.postal_code'))
                ->required(),
            CountrySelect::make('country_id')
                ->when(
                    $schema->isCreating(),
                    fn (CountrySelect $component): CountrySelect => $component->withCreateForm(),
                    fn (CountrySelect $component): CountrySelect => $component->withEditForm(),
                ),
            Grid::make()
                ->columnSpan(1)
                ->schema([
                    DefaultToggle::make('default'),
                    StatusToggle::make('status'),
                ]),
        ];
    }
}
