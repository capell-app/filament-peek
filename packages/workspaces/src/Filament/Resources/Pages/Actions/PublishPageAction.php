<?php

declare(strict_types=1);

namespace Capell\Workspaces\Filament\Resources\Pages\Actions;

use Capell\Core\Contracts\Pageable;
use Capell\Core\Models\Page;
use Capell\Workspaces\Enums\WorkspaceStatusEnum;
use Capell\Workspaces\Models\Workspace;
use Capell\Workspaces\Publisher;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Override;
use Throwable;

class PublishPageAction extends Action
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (Pageable $record): string => $this->buttonLabel($record))
            ->icon('heroicon-o-rocket-launch')
            ->color('primary')
            ->authorize(fn (Pageable $record): bool => $this->userCanPublish($record))
            ->visible(fn (Pageable $record): bool => $this->shouldBeVisible($record))
            ->disabled(fn (Pageable $record): bool => $this->shouldBeDisabled($record))
            ->requiresConfirmation()
            ->modalHeading(__('capell-admin::message.publish_heading'))
            ->modalDescription(fn (Pageable $record): HtmlString => new HtmlString($this->buildModalDescription($record)))
            ->action(function (Pageable $record): void {
                $workspace = $record->workspace;

                if (! $workspace instanceof Workspace) {
                    return;
                }

                try {
                    resolve(Publisher::class)->publish($workspace, auth()->user());
                } catch (Throwable $throwable) {
                    Notification::make()
                        ->title(__('capell-admin::workspace.notifications.publish_failed'))
                        ->body($throwable->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('capell-admin::message.published_notification', ['page' => $record->name]))
                    ->success()
                    ->send();

                $this->getLivewire()->redirectToLive();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'publish';
    }

    private function userCanPublish(Pageable $record): bool
    {
        $user = auth()->user();
        $workspace = $record->workspace;

        if ($user === null || ! $workspace instanceof Workspace) {
            return false;
        }

        if ($user->can('publish', $workspace)) {
            return true;
        }

        // Fallback for Open/InReview states where the publish policy gate
        // requires Approved status — a user that can update the page may
        // still see the (disabled / approval-pending) button.
        return $user->can('update', $record);
    }

    private function shouldBeVisible(Pageable $record): bool
    {
        if ($record->isLive()) {
            return false;
        }

        $status = $record->workspace?->status;

        return in_array($status, [
            WorkspaceStatusEnum::Open,
            WorkspaceStatusEnum::InReview,
            WorkspaceStatusEnum::Approved,
        ], true);
    }

    private function shouldBeDisabled(Pageable $record): bool
    {
        return $record->workspace?->status === WorkspaceStatusEnum::InReview;
    }

    private function buttonLabel(Pageable $record): string
    {
        $workspace = $record->workspace;

        if (! $workspace instanceof Workspace) {
            return __('capell-admin::button.publish');
        }

        $pageCount = Page::query()
            ->withoutGlobalScopes()
            ->where('workspace_id', $workspace->id)
            ->count();

        if ($pageCount > 1) {
            return __('capell-admin::button.publish_workspace_n_pages', ['count' => $pageCount]);
        }

        return __('capell-admin::button.publish');
    }

    private function buildModalDescription(Pageable $record): string
    {
        $workspace = $record->workspace;

        if (! $workspace instanceof Workspace) {
            return '';
        }

        if ($workspace->status === WorkspaceStatusEnum::InReview) {
            return __('capell-admin::message.publish_waiting_for_approval');
        }

        $pageCount = Page::query()
            ->withoutGlobalScopes()
            ->where('workspace_id', $workspace->id)
            ->count();

        if ($pageCount > 1) {
            return __('capell-admin::message.publish_multi_page', ['count' => $pageCount]);
        }

        return __('capell-admin::message.publish_single_page', ['page' => $record->name]);
    }
}
