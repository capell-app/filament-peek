<?php

declare(strict_types=1);

use Capell\Core\Facades\CapellCore;
use Capell\EmailStudio\Providers\EmailStudioServiceProvider;

it('registers the email studio package metadata and config', function (): void {
    $package = CapellCore::getPackage(EmailStudioServiceProvider::$packageName);

    expect($package->name)->toBe(EmailStudioServiceProvider::$packageName)
        ->and(config('capell-email-studio.tables.templates'))->toBe('email_templates')
        ->and(config('capell-email-studio.queue'))->toBe('default');
});
