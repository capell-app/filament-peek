<?php

declare(strict_types=1);

namespace Capell\LoginAudit\Tests\Fixtures\Autoload;

use Capell\Tests\Fixtures\Models\User;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;

final class LoginAuditBridgeTestUser extends User
{
    use AuthenticationLoggable;

    protected $table = 'users';
}
