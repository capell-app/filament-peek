<?php

declare(strict_types=1);

namespace Capell\Tags\Enums;

use Capell\Tags\Models\Tag;

enum ModelEnum: string
{
    case Tag = Tag::class;
}
