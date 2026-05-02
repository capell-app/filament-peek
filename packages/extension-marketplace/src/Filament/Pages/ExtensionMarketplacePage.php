<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Filament\Pages;

use BackedEnum;
use Capell\Core\Data\PackageData;
use Capell\Core\Facades\CapellCore;
use Capell\ExtensionMarketplace\Actions\CreateExtensionAcquisitionAction;
use Capell\ExtensionMarketplace\Actions\PhoneHomeAction;
use Capell\ExtensionMarketplace\Actions\StartMarketplaceRegistrationAction;
use Capell\ExtensionMarketplace\Actions\VerifyMarketplaceRegistrationAction;
use Capell\ExtensionMarketplace\Data\ExtensionListingData;
use Capell\ExtensionMarketplace\Enums\ExtensionKind;
use Capell\ExtensionMarketplace\Enums\MarketplaceExtensionCapability;
use Capell\ExtensionMarketplace\Enums\MarketplaceExtensionCategory;
use Capell\ExtensionMarketplace\Enums\MarketplaceExtensionSort;
use Capell\ExtensionMarketplace\Enums\MarketplaceRegistrationStatus;
use Capell\ExtensionMarketplace\Exceptions\PurchaseRequiredException;
use Capell\ExtensionMarketplace\Models\MarketplaceInstance;
use Capell\ExtensionMarketplace\Models\MarketplaceRegistrationSession;
use Capell\ExtensionMarketplace\Services\MarketplaceClient;
use Capell\ExtensionMarketplace\Services\VersionCompatibilityChecker;
use Capell\ExtensionMarketplace\Support\MarketplaceWebhookUrl;
use Composer\InstalledVersions;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Override;
use Throwable;

class ExtensionMarketplacePage extends Page implements HasActions, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;

    /** @var string */
    public const SLUG = 'capell-extension-marketplace';

    private const INSTALL_FAILED_NOTIFICATION_ID = 'capell-extension-marketplace-install-failed';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static ?int $navigationSort = 90;

    protected string $view = 'capell-extension-marketplace::filament.pages.extension-marketplace-page';

    protected static ?string $slug = 'capell-extension-marketplace';

    #[Override]
    public static function getNavigationLabel(): string
    {
        return (string) __('capell-extension-marketplace::navigation.extension_marketplace');
    }

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return (string) (__('capell-admin::navigation.group_administration'));
    }

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return (string) __('capell-extension-marketplace::marketplace.page.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (?string $search = null, ?array $filters = null): array => $this->getMarketplaceTableRecords($search, $filters))
            ->searchable()
            ->searchPlaceholder((string) __('capell-extension-marketplace::marketplace.filters.search_placeholder'))
            ->filters($this->getMarketplaceTableFilters(), FiltersLayout::AboveContentCollapsible)
            ->deferFilters(false)
            ->filtersFormColumns([
                'md' => 2,
                'xl' => 4,
            ])
            ->columns([
                Stack::make([
                    ViewColumn::make('plugin')
                        ->label((string) __('capell-extension-marketplace::marketplace.columns.plugin'))
                        ->view('capell-extension-marketplace::filament.tables.columns.marketplace-extension-pod'),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->recordActions($this->getMarketplaceTableActions(), RecordActionsPosition::AfterContent)
            ->paginated([9, 18, 36])
            ->defaultPaginationPageOption(9)
            ->emptyStateHeading((string) __('capell-extension-marketplace::marketplace.filters.empty_heading'))
            ->emptyStateDescription((string) __('capell-extension-marketplace::marketplace.filters.empty'));
    }

    /** @return array<int, ExtensionListingData> */
    public function getBrowseExtensions(): array
    {
        try {
            return array_values(array_filter(
                resolve(MarketplaceClient::class)->listExtensions(
                    sort: MarketplaceClient::DEFAULT_EXTENSION_SORT,
                ),
                fn (ExtensionListingData $extension): bool => ! $this->isHiddenMarketplaceExtension($extension),
            ));
        } catch (Throwable $throwable) {
            Log::warning('capell-extension-marketplace: marketplace browse failed', ['error' => $throwable->getMessage()]);

            return [];
        }
    }

    /**
     * @param  array<string, mixed>|null  $filters
     * @return array<int, array<string, mixed>>
     */
    public function getMarketplaceTableRecords(?string $search = null, ?array $filters = null): array
    {
        $filters ??= [];
        $compatibilityVersions = $this->detectedCompatibilityVersions();
        $kind = $this->filterValue($filters, 'kind');
        $sort = $this->validSort($this->filterValue($filters, 'sort') ?? MarketplaceClient::DEFAULT_EXTENSION_SORT);
        $installedStatus = $this->filterValue($filters, 'installed_status');
        $category = $this->validCategory($this->filterValue($filters, 'category'));
        $capabilities = $this->validCapabilities($this->filterValues($filters, 'capability'));
        $freeOnly = (bool) ($filters['free_only']['isActive'] ?? false);

        $extensions = $this->fetchMarketplaceExtensions(
            search: trim($search ?? ''),
            kind: $this->validKind($kind),
            freeOnly: $freeOnly,
            sort: $sort,
            priceMinCents: $this->moneyFilterToCents($filters['price']['price_min'] ?? null),
            priceMaxCents: $this->moneyFilterToCents($filters['price']['price_max'] ?? null),
            capellVersion: $this->filterValue($filters, 'compatibility', 'capell_version') ?? $compatibilityVersions['capell'],
            laravelVersion: $this->filterValue($filters, 'compatibility', 'laravel_version') ?? $compatibilityVersions['laravel'],
            livewireVersion: $this->filterValue($filters, 'compatibility', 'livewire_version') ?? $compatibilityVersions['livewire'],
            filamentVersion: $this->filterValue($filters, 'compatibility', 'filament_version') ?? $compatibilityVersions['filament'],
            category: $category,
            capabilities: $capabilities,
        );

        return collect($extensions)
            ->map(fn (ExtensionListingData $extension): array => $this->extensionTableRecord($extension))
            ->filter(fn (array $record): bool => match ($installedStatus) {
                'installed' => (bool) $record['is_installed'],
                'available' => ! (bool) $record['is_installed'],
                default => true,
            })
            ->values()
            ->all();
    }

    /** @return Collection<int, PackageData> */
    public function getInstalledExtensions(): Collection
    {
        return CapellCore::getInstalledPackages()->values();
    }

    /** @return array<int, string> */
    public function getInstalledComposerNames(): array
    {
        return $this->getInstalledExtensions()->pluck('name')->all();
    }

    public function isInstalled(ExtensionListingData $extension): bool
    {
        return in_array($extension->composerName, $this->getInstalledComposerNames(), true);
    }

    public function installedPluginVersion(string $composerName): ?string
    {
        if (! CapellCore::hasPackage($composerName)) {
            return null;
        }

        return CapellCore::getPackage($composerName)->version;
    }

    /** @return array<string, string> */
    public function getKindOptions(): array
    {
        return collect(ExtensionKind::cases())
            ->mapWithKeys(fn (ExtensionKind $kind): array => [$kind->value => $kind->getLabel()])
            ->all();
    }

    /** @return array<string, string> */
    public function getSortOptions(): array
    {
        return collect(MarketplaceExtensionSort::cases())
            ->mapWithKeys(fn (MarketplaceExtensionSort $sort): array => [$sort->value => $sort->getLabel()])
            ->all();
    }

    /** @return array<string, string> */
    public function getInstalledStatusOptions(): array
    {
        return [
            'available' => (string) __('capell-extension-marketplace::marketplace.filters.available_only'),
            'installed' => (string) __('capell-extension-marketplace::marketplace.filters.installed_only'),
        ];
    }

    /** @return array<string, string> */
    public function getCategoryOptions(): array
    {
        return collect(MarketplaceExtensionCategory::cases())
            ->mapWithKeys(fn (MarketplaceExtensionCategory $category): array => [$category->value => $category->getLabel()])
            ->all();
    }

    /** @return array<string, string> */
    public function getCapabilityOptions(): array
    {
        return collect(MarketplaceExtensionCapability::cases())
            ->mapWithKeys(fn (MarketplaceExtensionCapability $capability): array => [$capability->value => $capability->getLabel()])
            ->all();
    }

    /** @return array{capell: ?string, laravel: ?string, livewire: ?string, filament: ?string} */
    public function detectedCompatibilityVersions(): array
    {
        return [
            'capell' => CapellCore::getInstalledPrettyVersion('capell-app/capell')
                ?? CapellCore::getInstalledPrettyVersion('capell/core'),
            'laravel' => $this->installedPackagePrettyVersion('laravel/framework') ?? app()->version(),
            'livewire' => $this->installedPackagePrettyVersion('livewire/livewire'),
            'filament' => $this->installedPackagePrettyVersion('filament/filament'),
        ];
    }

    public function canShowInstalledExtensions(): bool
    {
        return true;
    }

    public function resetMarketplaceFilters(): void
    {
        $this->resetTable();
    }

    public function installExtensionAction(): Action
    {
        return Action::make('installExtension')
            ->label(fn (array $arguments): string => ($arguments['is_paid'] ?? false)
                ? (string) __('capell-extension-marketplace::marketplace.install.purchase_button')
                : (string) __('capell-extension-marketplace::marketplace.install.button'))
            ->requiresConfirmation(fn (array $arguments): bool => $this->shouldConfirmInstall($arguments))
            ->form(fn (array $arguments): array => $this->installExtensionFormSchema($arguments))
            ->modalHeading(fn (array $arguments): string => (string) __('capell-extension-marketplace::marketplace.install.modal_heading', ['name' => $arguments['name'] ?? '']))
            ->modalDescription(fn (array $arguments): Htmlable => $this->installExtensionModalDescription($arguments))
            ->modalSubmitActionLabel((string) __('capell-extension-marketplace::marketplace.install.confirm_button'))
            ->action(function (array $arguments, array $data): void {
                $this->installExtension($arguments, $data);
            });
    }

    public function connectMarketplaceAction(): Action
    {
        return Action::make('connectMarketplace')
            ->label((string) __('capell-extension-marketplace::marketplace.marketplace.connect_button'))
            ->icon(Heroicon::OutlinedLink)
            ->tooltip((string) __('capell-extension-marketplace::marketplace.marketplace.connect_tooltip'))
            ->disabled(fn (): bool => ! $this->canStartMarketplaceRegistration())
            ->action(fn (): mixed => $this->startMarketplaceRegistration());
    }

    public function verifyMarketplaceDomainAction(): Action
    {
        return Action::make('verifyMarketplaceDomain')
            ->label((string) __('capell-extension-marketplace::marketplace.marketplace.verify_button'))
            ->icon(Heroicon::OutlinedShieldCheck)
            ->color('success')
            ->tooltip((string) __('capell-extension-marketplace::marketplace.marketplace.verify_tooltip'))
            ->disabled(fn (): bool => ! $this->pendingMarketplaceRegistration() instanceof MarketplaceRegistrationSession)
            ->action(fn (): mixed => $this->verifyMarketplaceRegistration());
    }

    public function runMarketplaceHeartbeatAction(): Action
    {
        return Action::make('runMarketplaceHeartbeat')
            ->label((string) __('capell-extension-marketplace::marketplace.install.run_heartbeat'))
            ->icon(Heroicon::ArrowPath)
            ->color('gray')
            ->tooltip((string) __('capell-extension-marketplace::marketplace.marketplace.heartbeat_tooltip'))
            ->disabled(fn (): bool => ! $this->marketplaceInstance() instanceof MarketplaceInstance)
            ->action(fn (): mixed => $this->runMarketplaceHeartbeat());
    }

    public function marketplaceConnectionState(): string
    {
        if (! $this->marketplaceBaseUrlConfigured() || ! $this->marketplaceWebhookUrlConfigured()) {
            return 'needs_configuration';
        }

        if ($this->marketplaceInstance() instanceof MarketplaceInstance) {
            return 'connected';
        }

        if ($this->pendingMarketplaceRegistration() instanceof MarketplaceRegistrationSession) {
            return 'pending';
        }

        return 'not_connected';
    }

    public function marketplaceConnectionTitle(): string
    {
        return (string) __('capell-extension-marketplace::marketplace.marketplace.status.' . $this->marketplaceConnectionState() . '.title');
    }

    public function marketplaceConnectionBody(): string
    {
        return (string) __('capell-extension-marketplace::marketplace.marketplace.status.' . $this->marketplaceConnectionState() . '.body');
    }

    public function marketplaceInstance(): ?MarketplaceInstance
    {
        return MarketplaceInstance::query()
            ->latest('last_heartbeat_at')
            ->first();
    }

    public function pendingMarketplaceRegistration(): ?MarketplaceRegistrationSession
    {
        return MarketplaceRegistrationSession::query()
            ->whereIn('status', [
                MarketplaceRegistrationStatus::Pending,
                MarketplaceRegistrationStatus::Approved,
            ])
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();
    }

    public function installExtension(array $arguments, array $data = []): void
    {
        $listing = resolve(MarketplaceClient::class)->getExtension($arguments['slug']);

        if ($listing === null) {
            Notification::make()
                ->title((string) __('capell-extension-marketplace::marketplace.install.not_found'))
                ->warning()
                ->send();

            return;
        }

        try {
            $acquisition = CreateExtensionAcquisitionAction::run(
                listing: $listing,
                licenseKey: $data['license_key'] ?? null,
                email: $data['email'] ?? null,
                domain: null,
                installOptions: $this->selectedInstallOptionsFromData($listing, $data),
            );
        } catch (PurchaseRequiredException $exception) {
            Log::info('capell-extension-marketplace: marketplace purchase required', [
                'slug' => $arguments['slug'] ?? null,
                'purchase_url' => $exception->purchaseUrl,
            ]);

            Notification::make()
                ->title((string) __('capell-extension-marketplace::marketplace.install.purchase_required'))
                ->body($exception->getMessage())
                ->warning()
                ->persistent()
                ->actions([
                    Action::make('openPluginCheckout')
                        ->label((string) __('capell-extension-marketplace::marketplace.install.purchase_button'))
                        ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                        ->link()
                        ->url($exception->purchaseUrl, shouldOpenInNewTab: true),
                ])
                ->send();

            return;
        } catch (Throwable $throwable) {
            Log::warning('capell-extension-marketplace: marketplace install failed', [
                'slug' => $arguments['slug'] ?? null,
                'error' => $throwable->getMessage(),
            ]);

            $notification = Notification::make(self::INSTALL_FAILED_NOTIFICATION_ID)
                ->title((string) __('capell-extension-marketplace::marketplace.install.failed'))
                ->body($throwable->getMessage())
                ->danger();

            if ($throwable->getMessage() === MarketplaceClient::INSTANCE_NOT_REGISTERED_MESSAGE) {
                $notification
                    ->actions([
                        Action::make('connectMarketplace')
                            ->label((string) __('capell-extension-marketplace::marketplace.marketplace.connect_button'))
                            ->icon(Heroicon::OutlinedLink)
                            ->color('danger')
                            ->link()
                            ->close()
                            ->dispatch('connect-marketplace'),
                    ])
                    ->persistent();
            }

            $notification->send();

            return;
        }

        Notification::make()
            ->title((string) __('capell-extension-marketplace::marketplace.install.composer_command_ready'))
            ->body($acquisition->composerCommand)
            ->success()
            ->persistent()
            ->send();
    }

    #[On('run-marketplace-heartbeat')]
    public function runMarketplaceHeartbeat(): void
    {
        $phoneHome = resolve(PhoneHomeAction::class);

        if (! $phoneHome->handle()) {
            $notification = Notification::make()
                ->title((string) __('capell-extension-marketplace::marketplace.install.heartbeat_failed'))
                ->body((string) __('capell-extension-marketplace::marketplace.install.heartbeat_failed_body', [
                    'reason' => $phoneHome->failureMessage() ?? (string) __('capell-extension-marketplace::marketplace.install.heartbeat_default_failure'),
                ]))
                ->danger()
                ->persistent();

            $troubleshootingUrl = config('capell-extension-marketplace.marketplace.troubleshooting_url');

            if (is_string($troubleshootingUrl) && $troubleshootingUrl !== '') {
                $notification->actions([
                    Action::make('marketplaceHeartbeatDocs')
                        ->label((string) __('capell-extension-marketplace::marketplace.install.heartbeat_docs'))
                        ->icon(Heroicon::BookOpen)
                        ->link()
                        ->url($troubleshootingUrl, shouldOpenInNewTab: true),
                ]);
            }

            $notification->send();

            return;
        }

        Notification::make()
            ->title((string) __('capell-extension-marketplace::marketplace.install.heartbeat_completed'))
            ->success()
            ->send();

        $this->dispatch('close-notification', id: self::INSTALL_FAILED_NOTIFICATION_ID);
    }

    #[On('connect-marketplace')]
    public function startMarketplaceRegistration(): void
    {
        try {
            $session = StartMarketplaceRegistrationAction::run();
        } catch (Throwable $throwable) {
            Log::warning('capell-extension-marketplace: marketplace registration failed', ['error' => $throwable->getMessage()]);

            Notification::make()
                ->title((string) __('capell-extension-marketplace::marketplace.marketplace.connection_failed'))
                ->body($this->marketplaceRegistrationFailureMessage($throwable))
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        $notification = Notification::make()
            ->title((string) __('capell-extension-marketplace::marketplace.marketplace.connection_started'))
            ->body((string) __('capell-extension-marketplace::marketplace.marketplace.connection_started_body'))
            ->success()
            ->persistent();

        if (is_string($session->verification_url) && $session->verification_url !== '') {
            $notification->actions([
                Action::make('openMarketplaceApproval')
                    ->label((string) __('capell-extension-marketplace::marketplace.marketplace.open_marketplace'))
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->link()
                    ->url($session->verification_url, shouldOpenInNewTab: true),
            ]);
        }

        $notification->send();
    }

    public function verifyMarketplaceRegistration(): void
    {
        $session = MarketplaceRegistrationSession::query()
            ->whereIn('status', [
                MarketplaceRegistrationStatus::Pending,
                MarketplaceRegistrationStatus::Approved,
            ])
            ->latest()
            ->first();

        if ($session === null) {
            Notification::make()
                ->title((string) __('capell-extension-marketplace::marketplace.marketplace.no_registration'))
                ->warning()
                ->send();

            return;
        }

        try {
            VerifyMarketplaceRegistrationAction::run($session);
        } catch (Throwable $throwable) {
            Log::warning('capell-extension-marketplace: marketplace verification failed', ['error' => $throwable->getMessage()]);

            Notification::make()
                ->title((string) __('capell-extension-marketplace::marketplace.marketplace.verification_failed'))
                ->body($throwable->getMessage())
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        Notification::make()
            ->title((string) __('capell-extension-marketplace::marketplace.marketplace.connected'))
            ->success()
            ->send();
    }

    /** @return array<int, Filter|SelectFilter> */
    private function getMarketplaceTableFilters(): array
    {
        $compatibilityVersions = $this->detectedCompatibilityVersions();

        return [
            SelectFilter::make('kind')
                ->label((string) __('capell-extension-marketplace::marketplace.filters.type_label'))
                ->placeholder((string) __('capell-extension-marketplace::marketplace.filters.all_types'))
                ->options($this->getKindOptions())
                ->static(),
            SelectFilter::make('category')
                ->label((string) __('capell-extension-marketplace::marketplace.filters.category_label'))
                ->placeholder((string) __('capell-extension-marketplace::marketplace.filters.all_categories'))
                ->options($this->getCategoryOptions())
                ->static(),
            SelectFilter::make('capability')
                ->label((string) __('capell-extension-marketplace::marketplace.filters.capability_label'))
                ->placeholder((string) __('capell-extension-marketplace::marketplace.filters.all_capabilities'))
                ->options($this->getCapabilityOptions())
                ->multiple()
                ->static(),
            Filter::make('free_only')
                ->label((string) __('capell-extension-marketplace::marketplace.filters.free_only')),
            Filter::make('price')
                ->label((string) __('capell-extension-marketplace::marketplace.filters.price_range'))
                ->columns(2)
                ->schema([
                    TextInput::make('price_min')
                        ->label((string) __('capell-extension-marketplace::marketplace.filters.price_min'))
                        ->numeric()
                        ->prefix('$')
                        ->placeholder('0'),
                    TextInput::make('price_max')
                        ->label((string) __('capell-extension-marketplace::marketplace.filters.price_max'))
                        ->numeric()
                        ->prefix('$')
                        ->placeholder('99'),
                ]),
            SelectFilter::make('sort')
                ->label((string) __('capell-extension-marketplace::marketplace.filters.sort_label'))
                ->options($this->getSortOptions())
                ->default(MarketplaceClient::DEFAULT_EXTENSION_SORT)
                ->selectablePlaceholder(false)
                ->static(),
            SelectFilter::make('installed_status')
                ->label((string) __('capell-extension-marketplace::marketplace.filters.installed_status'))
                ->placeholder((string) __('capell-extension-marketplace::marketplace.filters.all_extensions'))
                ->options($this->getInstalledStatusOptions())
                ->static(),
            Filter::make('compatibility')
                ->label((string) __('capell-extension-marketplace::marketplace.filters.compatibility'))
                ->columns(2)
                ->schema([
                    Select::make('capell_version')
                        ->label((string) __('capell-extension-marketplace::marketplace.filters.capell_version'))
                        ->options($this->versionFilterOptions($compatibilityVersions['capell']))
                        ->default($compatibilityVersions['capell'])
                        ->searchable()
                        ->native(false),
                    Select::make('laravel_version')
                        ->label((string) __('capell-extension-marketplace::marketplace.filters.laravel_version'))
                        ->options($this->versionFilterOptions($compatibilityVersions['laravel']))
                        ->default($compatibilityVersions['laravel'])
                        ->searchable()
                        ->native(false),
                    Select::make('filament_version')
                        ->label((string) __('capell-extension-marketplace::marketplace.filters.filament_version'))
                        ->options($this->versionFilterOptions($compatibilityVersions['filament']))
                        ->default($compatibilityVersions['filament'])
                        ->searchable()
                        ->native(false),
                    Select::make('livewire_version')
                        ->label((string) __('capell-extension-marketplace::marketplace.filters.livewire_version'))
                        ->options($this->versionFilterOptions($compatibilityVersions['livewire']))
                        ->default($compatibilityVersions['livewire'])
                        ->searchable()
                        ->native(false),
                ]),
        ];
    }

    /** @return array<int, Action> */
    private function getMarketplaceTableActions(): array
    {
        return [
            Action::make('installMarketplaceExtension')
                ->label(fn (array $record): string => ($record['is_paid'] ?? false)
                    ? (string) __('capell-extension-marketplace::marketplace.install.purchase_button')
                    : (string) __('capell-extension-marketplace::marketplace.install.button'))
                ->icon(Heroicon::OutlinedCloudArrowDown)
                ->color(fn (array $record): string => ($record['is_paid'] ?? false) ? 'warning' : 'primary')
                ->tooltip((string) __('capell-extension-marketplace::marketplace.install.tooltip'))
                ->visible(fn (array $record): bool => ! (bool) ($record['is_installed'] ?? false))
                ->requiresConfirmation(fn (array $record): bool => $this->shouldConfirmInstall($record))
                ->form(fn (array $record): array => $this->installExtensionFormSchema($record))
                ->modalHeading(fn (array $record): string => (string) __('capell-extension-marketplace::marketplace.install.modal_heading', ['name' => $record['name'] ?? '']))
                ->modalDescription(fn (array $record): Htmlable => $this->installExtensionModalDescription($record))
                ->modalSubmitActionLabel((string) __('capell-extension-marketplace::marketplace.install.confirm_button'))
                ->action(function (array $record, array $data): void {
                    $this->installExtension($record, $data);
                }),
        ];
    }

    /**
     * @return array<int, ExtensionListingData>
     */
    private function fetchMarketplaceExtensions(
        string $search = '',
        string $kind = '',
        bool $freeOnly = false,
        string $sort = MarketplaceClient::DEFAULT_EXTENSION_SORT,
        ?int $priceMinCents = null,
        ?int $priceMaxCents = null,
        ?string $capellVersion = null,
        ?string $laravelVersion = null,
        ?string $livewireVersion = null,
        ?string $filamentVersion = null,
        ?string $category = null,
        array $capabilities = [],
    ): array {
        try {
            return array_values(array_filter(
                resolve(MarketplaceClient::class)->listExtensions(
                    search: $search,
                    kind: $kind,
                    freeOnly: $freeOnly,
                    sort: $sort,
                    priceMinCents: $priceMinCents,
                    priceMaxCents: $priceMaxCents,
                    capellVersion: $capellVersion,
                    laravelVersion: $laravelVersion,
                    livewireVersion: $livewireVersion,
                    filamentVersion: $filamentVersion,
                    category: $category,
                    capabilities: $capabilities,
                ),
                fn (ExtensionListingData $extension): bool => ! $this->isHiddenMarketplaceExtension($extension),
            ));
        } catch (Throwable $throwable) {
            Log::warning('capell-extension-marketplace: marketplace browse failed', ['error' => $throwable->getMessage()]);

            return [];
        }
    }

    /** @return array<string, mixed> */
    private function extensionTableRecord(ExtensionListingData $extension): array
    {
        $isInstalled = $this->isInstalled($extension);
        $installedVersion = $isInstalled ? $this->installedPluginVersion($extension->composerName) : null;
        $compatibilityDetails = resolve(VersionCompatibilityChecker::class)->compatibilityDetails($extension);

        return [
            'key' => $extension->slug,
            'slug' => $extension->slug,
            'name' => $extension->name,
            'composer_name' => $extension->composerName,
            'kind' => $extension->kind,
            'description' => $extension->description,
            'image_url' => $extension->imageUrl,
            'price_cents' => $extension->priceCents,
            'price_label' => $this->priceLabel($extension),
            'is_paid' => $extension->isPaid,
            'is_featured' => $extension->isFeatured,
            'featured_rank' => $extension->featuredRank,
            'is_publisher_verified' => $extension->publisherVerified,
            'is_security_reviewed' => $extension->securityReviewed,
            'latest_version' => $extension->latestVersion,
            'released_at_label' => $extension->releasedAt?->toFormattedDateString(),
            'is_installed' => $isInstalled,
            'installed_version' => $installedVersion,
            'has_update_available' => $this->hasUpdateAvailable($installedVersion, $extension->latestVersion),
            'documentation_url' => $extension->documentationUrl,
            'purchase_url' => $extension->purchaseUrl,
            'requires_confirmation' => $extension->requiresConfirmation,
            'install_confirmation' => $extension->installConfirmation,
            'install_options' => $extension->installOptions,
            'capell_version_constraint' => $extension->capellVersionConstraint,
            'laravel_version_constraint' => $extension->laravelVersionConstraint,
            'filament_version_constraint' => $extension->filamentVersionConstraint,
            'livewire_version_constraint' => $extension->livewireVersionConstraint,
            'category_labels' => $this->categoryLabels($extension->categories),
            'capability_labels' => $this->capabilityLabels($extension->capabilities),
            'is_compatible' => ! in_array('incompatible', $compatibilityDetails, true),
            'compatibility_warnings' => $this->compatibilityWarnings($compatibilityDetails),
        ];
    }

    private function priceLabel(ExtensionListingData $extension): string
    {
        if (! $extension->isPaid) {
            return (string) __('capell-extension-marketplace::marketplace.install.free');
        }

        return '$' . number_format($extension->priceCents / 100, 2);
    }

    private function hasUpdateAvailable(?string $installedVersion, ?string $latestVersion): bool
    {
        if (! $this->isComparableVersion($installedVersion) || ! $this->isComparableVersion($latestVersion)) {
            return false;
        }

        return version_compare(ltrim((string) $installedVersion, 'v'), ltrim((string) $latestVersion, 'v'), '<');
    }

    private function isComparableVersion(?string $version): bool
    {
        return is_string($version) && preg_match('/^v?\d+(?:\.\d+){0,3}(?:[-+][0-9A-Za-z.-]+)?$/', $version) === 1;
    }

    /**
     * @param  array<int, string>  $categories
     * @return array<int, string>
     */
    private function categoryLabels(array $categories): array
    {
        return collect($categories)
            ->map(fn (string $category): string => MarketplaceExtensionCategory::tryFrom($category)?->getLabel() ?? Str::headline($category))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $capabilities
     * @return array<int, string>
     */
    private function capabilityLabels(array $capabilities): array
    {
        return collect($this->capabilitySlugs($capabilities))
            ->map(fn (string $capability): string => MarketplaceExtensionCapability::tryFrom($capability)?->getLabel() ?? Str::headline($capability))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $capabilities
     * @return array<int, string>
     */
    private function capabilitySlugs(array $capabilities): array
    {
        $slugs = [];

        foreach ($capabilities as $capabilityKey => $capabilityValue) {
            if (is_string($capabilityKey) && $capabilityKey !== '' && $capabilityValue !== false && $capabilityValue !== null) {
                $slugs[] = Str::snake($capabilityKey);

                continue;
            }

            if (is_array($capabilityValue)) {
                $capabilitySlug = $capabilityValue['slug'] ?? $capabilityValue['key'] ?? null;

                if (is_scalar($capabilitySlug) && (string) $capabilitySlug !== '') {
                    $slugs[] = Str::snake((string) $capabilitySlug);
                }

                continue;
            }

            if (is_scalar($capabilityValue) && (string) $capabilityValue !== '') {
                $slugs[] = Str::snake((string) $capabilityValue);
            }
        }

        return array_values(array_unique($slugs));
    }

    /**
     * @param  array<string, string>  $compatibilityDetails
     * @return array<int, string>
     */
    private function compatibilityWarnings(array $compatibilityDetails): array
    {
        return collect($compatibilityDetails)
            ->filter(fn (string $status): bool => $status === 'incompatible')
            ->keys()
            ->map(fn (string $platform): string => (string) __('capell-extension-marketplace::marketplace.card.incompatible_platform', [
                'platform' => (string) __('capell-extension-marketplace::marketplace.platforms.' . $platform),
            ]))
            ->values()
            ->all();
    }

    private function filterValue(array $filters, string $filter, string $field = 'value'): ?string
    {
        $value = $filters[$filter][$field] ?? null;

        return is_scalar($value) && (string) $value !== ''
            ? (string) $value
            : null;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, string>
     */
    private function filterValues(array $filters, string $filter): array
    {
        $values = $filters[$filter]['values'] ?? [];

        if (! is_array($values)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn (mixed $value): ?string => is_scalar($value) && (string) $value !== '' ? (string) $value : null,
            $values,
        ), is_string(...)));
    }

    private function moneyFilterToCents(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $amount = (float) $value;

        if ($amount < 0) {
            return null;
        }

        return (int) round($amount * 100);
    }

    /** @return array<string, string> */
    private function versionFilterOptions(?string $version): array
    {
        return is_string($version) && $version !== ''
            ? [$version => $version]
            : [];
    }

    private function installedPackagePrettyVersion(string $packageName): ?string
    {
        try {
            return InstalledVersions::isInstalled($packageName)
                ? InstalledVersions::getPrettyVersion($packageName)
                : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function marketplaceRegistrationFailureMessage(Throwable $throwable): string
    {
        if ($throwable instanceof RequestException) {
            $webhookErrors = $throwable->response->json('errors.webhook_url');

            if (is_array($webhookErrors) && $webhookErrors !== []) {
                return (string) __('capell-extension-marketplace::marketplace.marketplace.webhook_url_required');
            }

            $message = $throwable->response->json('message');

            if (is_string($message) && $message !== '') {
                return $message;
            }
        }

        return $throwable->getMessage();
    }

    private function canStartMarketplaceRegistration(): bool
    {
        return $this->marketplaceBaseUrlConfigured() && $this->marketplaceWebhookUrlConfigured();
    }

    private function marketplaceBaseUrlConfigured(): bool
    {
        $baseUrl = config('capell-extension-marketplace.marketplace.base_url');

        return is_string($baseUrl) && $baseUrl !== '';
    }

    private function marketplaceWebhookUrlConfigured(): bool
    {
        return MarketplaceWebhookUrl::isAvailable();
    }

    private function validKind(?string $kind): string
    {
        return ExtensionKind::tryFrom((string) $kind) instanceof ExtensionKind
            ? (string) $kind
            : '';
    }

    private function validSort(?string $sort): string
    {
        return MarketplaceExtensionSort::tryFrom((string) $sort) instanceof MarketplaceExtensionSort
            ? (string) $sort
            : MarketplaceClient::DEFAULT_EXTENSION_SORT;
    }

    private function validCategory(?string $category): ?string
    {
        return MarketplaceExtensionCategory::tryFrom((string) $category) instanceof MarketplaceExtensionCategory
            ? (string) $category
            : null;
    }

    /**
     * @param  array<int, string>  $capabilities
     * @return array<int, string>
     */
    private function validCapabilities(array $capabilities): array
    {
        return array_values(array_filter(
            $capabilities,
            fn (string $capability): bool => MarketplaceExtensionCapability::tryFrom($capability) instanceof MarketplaceExtensionCapability,
        ));
    }

    private function isHiddenMarketplaceExtension(ExtensionListingData $extension): bool
    {
        $hiddenComposerNames = config('capell-extension-marketplace.marketplace.hidden_composer_names', []);

        return in_array($extension->composerName, $hiddenComposerNames, true);
    }

    /** @return array<int, mixed> */
    private function installExtensionFormSchema(array $arguments): array
    {
        $schema = [];

        if ($arguments['is_paid'] ?? false) {
            $schema[] = TextInput::make('email')
                ->label((string) __('capell-extension-marketplace::marketplace.install.email_label'))
                ->email()
                ->required();
        }

        foreach ($this->installOptionsFromArguments($arguments) as $option) {
            $field = $this->installOptionField($option);

            if ($field instanceof Field) {
                $schema[] = $field;
            }
        }

        return $schema;
    }

    private function shouldConfirmInstall(array $arguments): bool
    {
        return ($arguments['requires_confirmation'] ?? false) === true
            || ($arguments['is_paid'] ?? false) === true
            || $this->installOptionsFromArguments($arguments) !== [];
    }

    private function installExtensionModalDescription(array $arguments): Htmlable
    {
        $confirmation = is_array($arguments['install_confirmation'] ?? null)
            ? $arguments['install_confirmation']
            : [];
        $summary = is_string($confirmation['summary'] ?? null)
            ? $confirmation['summary']
            : (string) __('capell-extension-marketplace::marketplace.install.default_confirmation');
        $details = array_filter(
            (array) ($confirmation['details'] ?? []),
            is_string(...),
        );
        $documentationUrl = is_string($arguments['documentation_url'] ?? null)
            ? $arguments['documentation_url']
            : null;

        $html = '<div class="space-y-3">';
        $html .= '<p>' . e($summary) . '</p>';

        if ($details !== []) {
            $html .= '<ul class="list-disc space-y-1 ps-5">';

            foreach ($details as $detail) {
                $html .= '<li>' . e($detail) . '</li>';
            }

            $html .= '</ul>';
        }

        if ($documentationUrl !== null && $documentationUrl !== '') {
            $html .= '<p><a href="' . e($documentationUrl) . '" target="_blank" rel="noopener noreferrer" class="text-primary-600 font-medium underline underline-offset-4">';
            $html .= e((string) __('capell-extension-marketplace::marketplace.install.documentation_link'));
            $html .= '</a></p>';
        }

        $html .= '</div>';

        return new HtmlString($html);
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<int, array<string, mixed>>
     */
    private function installOptionsFromArguments(array $arguments): array
    {
        return array_values(array_filter(
            (array) ($arguments['install_options'] ?? []),
            fn (mixed $option): bool => is_array($option) && is_string($option['key'] ?? null) && $option['key'] !== '',
        ));
    }

    /** @param array<string, mixed> $option */
    private function installOptionField(array $option): ?Field
    {
        $key = (string) $option['key'];
        $type = (string) ($option['type'] ?? 'checkbox');
        $label = (string) ($option['label'] ?? Str::headline($key));
        $fieldName = 'install_options.' . $key;

        $field = match ($type) {
            'radio' => Radio::make($fieldName)->options($this->installOptionChoices($option)),
            'checkboxes', 'checkbox_list' => CheckboxList::make($fieldName)->options($this->installOptionChoices($option)),
            'checkbox' => Checkbox::make($fieldName),
            default => null,
        };

        if ($field === null) {
            return null;
        }

        $field->label($label);

        if (isset($option['description']) && is_string($option['description'])) {
            $field->helperText($option['description']);
        }

        if (array_key_exists('default', $option)) {
            $field->default($option['default']);
        }

        if (($option['required'] ?? false) === true) {
            $field instanceof Checkbox
                ? $field->accepted()
                : $field->required();
        }

        return $field;
    }

    /** @return array<string, string> */
    private function installOptionChoices(array $option): array
    {
        $choices = [];

        foreach ((array) ($option['options'] ?? []) as $value => $label) {
            if (is_array($label)) {
                $choiceValue = $label['value'] ?? null;
                $choiceLabel = $label['label'] ?? null;

                if (is_scalar($choiceValue) && is_scalar($choiceLabel)) {
                    $choices[(string) $choiceValue] = (string) $choiceLabel;
                }

                continue;
            }

            if (is_scalar($label)) {
                $choices[(string) $value] = (string) $label;
            }
        }

        return $choices;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function selectedInstallOptionsFromData(ExtensionListingData $listing, array $data): array
    {
        $selected = is_array($data['install_options'] ?? null)
            ? $data['install_options']
            : [];

        return collect($listing->installOptions)
            ->mapWithKeys(function (array $option) use ($selected): array {
                $key = $option['key'] ?? null;

                if (! is_string($key) || $key === '' || ! array_key_exists($key, $selected)) {
                    return [];
                }

                return [$key => $selected[$key]];
            })
            ->all();
    }
}
