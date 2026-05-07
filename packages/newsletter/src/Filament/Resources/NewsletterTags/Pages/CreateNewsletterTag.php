<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\NewsletterTags\Pages;

use Capell\Newsletter\Filament\Resources\NewsletterTags\NewsletterTagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletterTag extends CreateRecord
{
    protected static string $resource = NewsletterTagResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = config('capell-newsletter.newsletter_tag_type', 'newsletter');

        return $data;
    }
}
