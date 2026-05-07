<?php

declare(strict_types=1);

namespace Capell\Newsletter\Contracts;

use Capell\Newsletter\Models\Segment;
use Capell\Newsletter\Models\Subscriber;
use Illuminate\Database\Eloquent\Builder;

interface NewsletterSegmentProvider
{
    /**
     * @return Builder<Subscriber>
     */
    public function querySubscribers(Segment $segment): Builder;
}
