<?php

declare(strict_types=1);

namespace Capell\PublicActions\Filament\Resources\Submissions\Pages;

use Capell\PublicActions\Filament\Resources\Submissions\PublicActionSubmissionResource;
use Filament\Resources\Pages\ListRecords;

final class ListPublicActionSubmissions extends ListRecords
{
    protected static string $resource = PublicActionSubmissionResource::class;
}
