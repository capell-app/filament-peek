<?php

declare(strict_types=1);

use Capell\PublicActions\Support\PublicActionHandlerRegistry;
use Capell\PublicActions\Tests\Fakes\FakePublicActionHandler;
use Capell\PublicActions\Tests\Fakes\FakeValidationPublicActionHandler;
use Capell\PublicActions\Tests\PublicActionsTestCase;

uses(PublicActionsTestCase::class)->in(__DIR__);

beforeEach(function (): void {
    $registry = resolve(PublicActionHandlerRegistry::class);
    $registry->register('test.handler', FakePublicActionHandler::class);
    $registry->register('test.validation-handler', FakeValidationPublicActionHandler::class);
});
