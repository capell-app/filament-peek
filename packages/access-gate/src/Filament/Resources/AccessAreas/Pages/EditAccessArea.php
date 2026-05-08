<?php

declare(strict_types=1);

namespace Capell\AccessGate\Filament\Resources\AccessAreas\Pages;

use Capell\AccessGate\Actions\UpdateAccessGateAreaStatusAction;
use Capell\AccessGate\Enums\AccessAreaStatus;
use Capell\AccessGate\Filament\Resources\AccessAreas\AccessAreaResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditAccessArea extends EditRecord
{
    protected static string $resource = AccessAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pause')
                ->label(__('capell-access-gate::filament.actions.pause'))
                ->visible(fn (): bool => $this->record->status === AccessAreaStatus::Active)
                ->action(fn (): mixed => UpdateAccessGateAreaStatusAction::run($this->record, AccessAreaStatus::Paused)),
            Action::make('resume')
                ->label(__('capell-access-gate::filament.actions.resume'))
                ->visible(fn (): bool => $this->record->status === AccessAreaStatus::Paused)
                ->action(fn (): mixed => UpdateAccessGateAreaStatusAction::run($this->record, AccessAreaStatus::Active)),
            DeleteAction::make(),
        ];
    }
}
