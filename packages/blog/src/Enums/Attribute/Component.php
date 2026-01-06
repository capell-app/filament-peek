<?php

declare(strict_types=1);

namespace Capell\Blog\Enums\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class Component
{
    public function __construct(public string $class) {}
}
