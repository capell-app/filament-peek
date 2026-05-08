<?php

declare(strict_types=1);

use Capell\Diagnostics\Filament\Pages\CommandPalettePage;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate(config('capell.roles.super_admin', 'super_admin'));
});

it('allows super admins to access the command palette', function (): void {
    $user = $this->createUserWithRole(config('capell.roles.super_admin', 'super_admin'));

    $this->actingAs($user);

    expect(CommandPalettePage::canAccess())->toBeTrue();
});
