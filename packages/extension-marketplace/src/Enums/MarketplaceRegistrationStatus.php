<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Enums;

enum MarketplaceRegistrationStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Verified = 'verified';
    case Expired = 'expired';
    case Failed = 'failed';
    case Revoked = 'revoked';
}
