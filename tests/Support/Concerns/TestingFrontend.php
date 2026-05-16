<?php

declare(strict_types=1);

namespace Capell\Tests\Support\Concerns;

use Capell\Core\ThemeStudio\Theme\ThemeRegistry;
use Capell\Tests\AbstractTestCase;
use Illuminate\Support\Facades\App;
use Livewire\Blaze\Blaze;

/**
 * @mixin AbstractTestCase
 */
trait TestingFrontend
{
    public function setUpTestingFrontend(): void
    {
        if (! App::environment('testing')) {
            return;
        }

        if (class_exists(Blaze::class)) {
            Blaze::disable();
            Blaze::optimize()->clear();
        }

        // Clear page cache storage if present
        /*try {
            $pageCache = resolve(PageCacheService::class);
            if ($pageCache->exists('')) {
                $pageCache->deleteDirectory('/');
            }
        } catch (Throwable) {
            // ignore
        }*/

        // Optionally could register routes if needed
        // \Capell\Frontend\Helpers\Routes::routes();

        $this->withoutVite();

        if (class_exists(ThemeRegistry::class) && $this->app->bound(ThemeRegistry::class)) {
            resolve(ThemeRegistry::class)->reset();
        }
    }
}
