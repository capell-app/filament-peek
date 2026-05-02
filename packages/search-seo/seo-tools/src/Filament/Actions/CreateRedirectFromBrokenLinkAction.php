<?php

declare(strict_types=1);

namespace Capell\SeoTools\Filament\Actions;

use Capell\Core\Enums\RedirectStatusCodeEnum;
use Capell\Redirects\Actions\ValidateRedirectAction;
use Capell\Redirects\Filament\Resources\Redirects\RedirectResource;
use Capell\SeoTools\Actions\CreateRedirectForBrokenLinkAction;
use Capell\SeoTools\Models\BrokenLink;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class CreateRedirectFromBrokenLinkAction extends Action
{
    private const REDIRECT_RESOURCE = RedirectResource::class;

    private const VALIDATE_REDIRECT_ACTION = ValidateRedirectAction::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->name('create_redirect')
            ->label(__('redirects::generic.redirect'))
            ->icon('heroicon-o-arrow-uturn-right')
            ->visible(fn (): bool => $this->redirectsAreInstalled())
            ->disabled(fn (): bool => ! $this->redirectsAreInstalled())
            ->modalHeading(__('capell-seo-tools::generic.redirect_create_modal_heading'))
            ->modalSubmitActionLabel(__('capell-seo-tools::generic.redirect_create_submit'))
            ->schema(fn (BrokenLink $record): array => $this->formSchema($record))
            ->action($this->createRedirect(...));
    }

    private function redirectsAreInstalled(): bool
    {
        return class_exists(self::VALIDATE_REDIRECT_ACTION)
            && class_exists(self::REDIRECT_RESOURCE)
            && method_exists(self::REDIRECT_RESOURCE, 'getUrl');
    }

    /**
     * @return array<int, Hidden|TextInput|Select|Textarea>
     */
    private function formSchema(BrokenLink $brokenLink): array
    {
        return [
            Hidden::make('source_url')
                ->default($this->sourceUrl($brokenLink)),
            TextInput::make('source_url_display')
                ->label(__('redirects::form.source_url'))
                ->default($this->sourceUrl($brokenLink))
                ->disabled()
                ->dehydrated(false),
            TextInput::make('target_url')
                ->label(__('redirects::form.target_url'))
                ->placeholder(__('redirects::form.target_url_placeholder'))
                ->required(),
            Select::make('status_code')
                ->label(__('redirects::form.status_code'))
                ->options(RedirectStatusCodeEnum::class)
                ->default(RedirectStatusCodeEnum::Permanent->value)
                ->required(),
            Textarea::make('notes')
                ->label(__('redirects::form.notes'))
                ->rows(3),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createRedirect(BrokenLink $record, array $data): void
    {
        CreateRedirectForBrokenLinkAction::run(
            brokenLink: $record,
            sourceUrl: (string) ($data['source_url'] ?? ''),
            targetUrl: (string) ($data['target_url'] ?? ''),
            statusCode: $this->statusCode($data['status_code'] ?? null),
            notes: $this->notes($data['notes'] ?? null),
        );

        Notification::make('redirect-created-from-broken-link')
            ->title(__('capell-seo-tools::generic.redirect_created'))
            ->success()
            ->send();
    }

    private function sourceUrl(BrokenLink $brokenLink): string
    {
        $targetUrl = trim($brokenLink->target_url);

        if (str_starts_with($targetUrl, '/')) {
            return $targetUrl;
        }

        $path = parse_url($targetUrl, PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            return '/';
        }

        $query = parse_url($targetUrl, PHP_URL_QUERY);

        return is_string($query) && $query !== ''
            ? $path . '?' . $query
            : $path;
    }

    private function statusCode(mixed $value): RedirectStatusCodeEnum
    {
        if ($value instanceof RedirectStatusCodeEnum) {
            return $value;
        }

        if (is_scalar($value)) {
            return RedirectStatusCodeEnum::tryFrom((int) $value) ?? RedirectStatusCodeEnum::Permanent;
        }

        return RedirectStatusCodeEnum::Permanent;
    }

    private function notes(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $notes = trim((string) $value);

        return $notes !== '' ? $notes : null;
    }
}
