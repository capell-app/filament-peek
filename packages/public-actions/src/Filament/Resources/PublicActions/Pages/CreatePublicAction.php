<?php

declare(strict_types=1);

namespace Capell\PublicActions\Filament\Resources\PublicActions\Pages;

use Capell\PublicActions\Filament\Resources\PublicActions\PublicActionResource;
use Filament\Resources\Pages\CreateRecord;

final class CreatePublicAction extends CreateRecord
{
    protected static string $resource = PublicActionResource::class;
}
