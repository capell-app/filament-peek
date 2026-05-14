<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Filament\Widgets;

use Capell\Admin\Contracts\CapellWidgetContract;
use Capell\Admin\Filament\Concerns\GatedByRoleAndSettings;
use Capell\SeoSuite\Actions\Dashboard\BuildSeoOpportunityRowsAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

final class SeoOpportunitiesWidget extends BaseWidget implements CapellWidgetContract
{
    use GatedByRoleAndSettings;

    /** @var list<string> */
    protected static array $rolesConfigKeys = ['admin', 'super_admin'];

    protected static string $settingsKey = 'seo_opportunities';

    /** @var int|string|array<string, int|string|null> */
    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 1];

    protected static ?int $sort = 44;

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): Collection => BuildSeoOpportunityRowsAction::run(5))
            ->queryStringIdentifier('seo-opportunities')
            ->paginated(false)
            ->searchable(false)
            ->heading(__('capell-seo-suite::dashboard.seo_opportunities'))
            ->columns([
                TextColumn::make('page')
                    ->label(__('capell-seo-suite::dashboard.page'))
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('score')
                    ->label(__('capell-seo-suite::dashboard.score'))
                    ->numeric(),
                TextColumn::make('critical_count')
                    ->label(__('capell-seo-suite::dashboard.critical'))
                    ->numeric(),
                TextColumn::make('warning_count')
                    ->label(__('capell-seo-suite::dashboard.warnings'))
                    ->numeric(),
                TextColumn::make('notices')
                    ->label(__('capell-seo-suite::dashboard.notices'))
                    ->numeric(),
            ]);
    }
}
