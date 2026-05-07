<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\NewsletterTags\Pages;

use Capell\Newsletter\Filament\Resources\NewsletterTags\NewsletterTagResource;
use Filament\Resources\Pages\EditRecord;

class EditNewsletterTag extends EditRecord
{
    protected static string $resource = NewsletterTagResource::class;
}
