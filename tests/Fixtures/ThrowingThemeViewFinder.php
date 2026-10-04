<?php

declare(strict_types=1);

namespace Capell\FilamentPeek\Tests\Fixtures;

use Illuminate\View\FileViewFinder;
use Override;
use RuntimeException;

final class ThrowingThemeViewFinder extends FileViewFinder
{
    public bool $throwOnReplace = true;

    #[Override]
    public function replaceNamespace(mixed $namespace, mixed $hints): void
    {
        parent::replaceNamespace($namespace, $hints);

        if ($this->throwOnReplace) {
            $this->throwOnReplace = false;

            throw new RuntimeException('Theme namespace registration failed after replacement.');
        }
    }
}
