<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\PublishingStudio\Enums\PublishingStudioPermission;
use Lorisleiva\Actions\Concerns\AsObject;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

final class EnsurePublishingStudioPermissionsAction
{
    use AsObject;

    public function handle(?string $guardName = null): void
    {
        $guard = $guardName ?? config('auth.defaults.guard', 'web');

        foreach (PublishingStudioPermission::cases() as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission->value,
                'guard_name' => $guard,
            ]);
        }

        resolve(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
