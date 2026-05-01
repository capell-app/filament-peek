<?php

declare(strict_types=1);

namespace Capell\SeoTools\Filament\Actions;

use Capell\SeoTools\Models\BrokenLink;
use Filament\Actions\Action;

class CreateRedirectFromBrokenLinkAction extends Action
{
    private const REDIRECT_RESOURCE = 'Capell\\Redirects\\Filament\\Resources\\Redirects\\RedirectResource';

    private const VALIDATE_REDIRECT_ACTION = 'Capell\\Redirects\\Actions\\ValidateRedirectAction';

    protected function setUp(): void
    {
        parent::setUp();

        $this->name('create_redirect')
            ->label(__('redirects::generic.redirect'))
            ->icon('heroicon-o-arrow-uturn-right')
            ->visible(fn (): bool => $this->redirectsAreInstalled())
            ->disabled(fn (): bool => ! $this->redirectsAreInstalled())
            ->openUrlInNewTab()
            ->url(fn (BrokenLink $record): ?string => $this->redirectCreateUrl($record));
    }

    private function redirectsAreInstalled(): bool
    {
        return class_exists(self::VALIDATE_REDIRECT_ACTION)
            && class_exists(self::REDIRECT_RESOURCE)
            && method_exists(self::REDIRECT_RESOURCE, 'getUrl');
    }

    private function redirectCreateUrl(BrokenLink $brokenLink): ?string
    {
        if (! $this->redirectsAreInstalled()) {
            return null;
        }

        /** @var class-string $resource */
        $resource = self::REDIRECT_RESOURCE;

        return $resource::getUrl();
    }
}
