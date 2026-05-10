<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Extenders;

use Capell\Admin\Contracts\Extenders\PageExportExtender;
use Capell\PublishingStudio\Models\Workspace;
use Carbon\CarbonInterface;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;

class PublishingStudioPageExportExtender implements PageExportExtender
{
    /** @return array<int, Component> */
    public function getFormFields(): array
    {
        return [
            Select::make('source_workspace_id')
                ->label(__('capell-admin::exchanger.export.source_workspace'))
                ->options(fn (): array => $this->workspaceOptions())
                ->placeholder(__('capell-admin::exchanger.export.source_live'))
                ->helperText(__('capell-admin::exchanger.export.source_workspace_help'))
                ->getSearchResultsUsing(fn (string $search): array => $this->workspaceOptions($search))
                ->getOptionLabelUsing(fn (mixed $value): ?string => $this->workspaceLabel($value))
                ->searchable()
                ->native(false),
        ];
    }

    /** @return array<string, mixed> */
    public function resolveOptions(array $data): array
    {
        $workspaceId = $data['source_workspace_id'] ?? null;

        if ($workspaceId === null || $workspaceId === '') {
            return ['source_workspace_id' => null];
        }

        $workspace = Workspace::query()->findOrFail((int) $workspaceId);

        throw_if(auth()->user()?->can('view', $workspace) !== true, AuthorizationException::class);

        return [
            'source_workspace_id' => $workspace->getKey(),
        ];
    }

    /** @return array<int|string, string> */
    private function workspaceOptions(?string $search = null): array
    {
        if (auth()->user()?->can('viewAny', Workspace::class) !== true) {
            return [];
        }

        return Workspace::query()
            ->when(
                is_string($search) && $search !== '',
                static fn (Builder $query): Builder => $query->where('name', 'like', $search . '%'),
            )
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name', 'kind', 'status', 'updated_at'])
            ->mapWithKeys(fn (Workspace $workspace): array => [
                $workspace->getKey() => $this->formatWorkspaceLabel($workspace),
            ])
            ->all();
    }

    private function workspaceLabel(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $workspace = Workspace::query()->find((int) $value);

        if (! $workspace instanceof Workspace || auth()->user()?->can('view', $workspace) !== true) {
            return null;
        }

        return $this->formatWorkspaceLabel($workspace);
    }

    private function formatWorkspaceLabel(Workspace $workspace): string
    {
        $updatedAt = $workspace->updated_at;

        return (string) __('capell-admin::exchanger.export.source_workspace_option', [
            'name' => $workspace->name,
            'kind' => $workspace->kind->getLabel(),
            'status' => $workspace->status->getLabel(),
            'updated_at' => $updatedAt instanceof CarbonInterface ? $updatedAt->toDateTimeString() : '',
        ]);
    }
}
