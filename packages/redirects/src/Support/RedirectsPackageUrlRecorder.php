<?php

declare(strict_types=1);

namespace Capell\Redirects\Support;

use Capell\Core\Contracts\Pageable;
use Capell\Core\Contracts\Redirects\RedirectUrlRecorder as RedirectUrlRecorderContract;
use Capell\Core\Models\Language;
use Capell\Redirects\Actions\AddRedirectUrlAction;

class RedirectsPackageUrlRecorder implements RedirectUrlRecorderContract
{
    public function record(Pageable $pageable, Language $language, string $url): void
    {
        AddRedirectUrlAction::run($pageable, $language, $url);
    }
}
