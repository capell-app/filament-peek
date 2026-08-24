<?php

declare(strict_types=1);

namespace Capell\FilamentPeek\Filament\Actions;

use Capell\Admin\Filament\Resources\Pages\Pages\EditPage;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Models\Page;
use Capell\FilamentPeek\Actions\CreatePagePreviewSnapshotAction;
use Capell\FilamentPeek\Providers\FilamentPeekServiceProvider;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

final class PeekPagePreviewAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('capell-filament-peek::actions.preview.label'))
            ->tooltip(__('capell-filament-peek::actions.preview.tooltip'))
            ->icon(Heroicon::OutlinedEye)
            ->color('gray')
            ->authorize(fn (?Model $record): bool => $record instanceof Page && Gate::allows('update', $record))
            ->visible(fn (?Model $record): bool => $record instanceof Page && CapellCore::isPackageInstalled(FilamentPeekServiceProvider::$packageName))
            ->action(function (Model $record, EditPage $livewire): void {
                if (! $record instanceof Page) {
                    return;
                }

                $formState = $this->formState($livewire);

                $snapshot = CreatePagePreviewSnapshotAction::run(
                    page: $record,
                    formState: $formState,
                );

                $livewire->dispatch(
                    'open-capell-page-preview-modal',
                    modalTitle: __('capell-filament-peek::actions.preview.modal_title'),
                    scopeLabel: __('capell-filament-peek::actions.preview.scope_label'),
                    subjectLabel: $this->subjectLabel($record, $formState),
                    ttlLabel: __('capell-filament-peek::actions.preview.ttl_notice', [
                        'minutes' => $this->previewTtlMinutes(),
                    ]),
                    iframeUrl: $snapshot['url'],
                    livewireId: $livewire->getId(),
                );
            });
    }

    public static function getDefaultName(): string
    {
        return 'peekPagePreview';
    }

    /**
     * @return array<string, mixed>
     */
    private function formState(EditPage $livewire): array
    {
        return $livewire->data ?? [];
    }

    /**
     * @param  array<string, mixed>  $formState
     */
    private function subjectLabel(Page $record, array $formState): string
    {
        $name = $formState['name'] ?? null;

        if (is_string($name) && trim($name) !== '') {
            return $name;
        }

        return (string) ($record->name ?? '');
    }

    private function previewTtlMinutes(): int
    {
        $minutes = config('capell-filament-peek.preview.ttl_minutes', 15);

        return is_int($minutes) && $minutes > 0 ? $minutes : 15;
    }
}
