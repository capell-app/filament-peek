<?php

declare(strict_types=1);

namespace Capell\AccessGate\Data;

use Capell\AccessGate\Models\Area;
use Capell\AccessGate\Models\BrowserToken;
use Capell\AccessGate\Models\Grant;

final class AccessGateAccessResultData
{
    public function __construct(
        public readonly bool $allowed,
        public readonly ?Area $area = null,
        public readonly ?Grant $grant = null,
        public readonly ?BrowserToken $browserToken = null,
    ) {}
}
