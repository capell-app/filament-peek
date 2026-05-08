<?php

declare(strict_types=1);

namespace Capell\AccessGate\Data;

use Capell\AccessGate\Models\BrowserToken;
use Capell\AccessGate\Models\ClaimToken;

final class IssuedAccessGateTokenData
{
    public function __construct(
        public readonly string $plainTextToken,
        public readonly BrowserToken|ClaimToken $token,
    ) {}
}
