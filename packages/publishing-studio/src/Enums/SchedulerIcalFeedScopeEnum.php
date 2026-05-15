<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Enums;

enum SchedulerIcalFeedScopeEnum: string
{
    case Mine = 'mine';
    case Site = 'site';
    case All = 'all';
}
