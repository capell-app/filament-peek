<?php

declare(strict_types=1);

namespace Capell\Layout\Observers;

use Capell\Core\Models\Type;
use Capell\Layout\Models\Content;
use Illuminate\Support\Str;

class ContentObserver
{
    public function creating(Content $content): void
    {
        if (! $content->type_id) {
            $content->type_id = Type::contentType()->value('id');
        }
    }

    public function replicating(Content $content): void
    {
        $content->uuid = Str::uuid();
    }
}
