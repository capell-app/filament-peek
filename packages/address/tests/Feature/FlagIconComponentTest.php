<?php

declare(strict_types=1);

use Capell\Address\Tests\AddressTestCase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;

require_once __DIR__ . '/../AddressTestCase.php';

uses(AddressTestCase::class);

beforeEach(function (): void {
    File::deleteDirectory(public_path('vendor/blade-country-flags'));
});

afterEach(function (): void {
    File::deleteDirectory(public_path('vendor/blade-country-flags'));
});

it('renders a published blade country flag asset', function (): void {
    File::ensureDirectoryExists(public_path('vendor/blade-country-flags'));
    File::put(public_path('vendor/blade-country-flags/4x3-fr.svg'), '<svg></svg>');

    $html = Blade::render('<x-capell-address::flag-icon flag="flag-4x3-fr" label="France" class="h-4" />');

    expect($html)
        ->toContain('src="http://localhost/vendor/blade-country-flags/4x3-fr.svg"')
        ->toContain('alt="France"')
        ->toContain('h-4')
        ->not->toContain('Missing flag');
});

it('falls back to a label when the published flag asset is missing', function (): void {
    $html = Blade::render('<x-capell-address::flag-icon flag="flag-4x3-gb-eng" label="England" />');

    expect($html)
        ->toContain('England')
        ->not->toContain('<img');
});
