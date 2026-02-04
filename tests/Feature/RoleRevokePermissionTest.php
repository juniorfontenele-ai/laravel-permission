<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\DB;
use JuniorFontenele\LaravelPermission\Models\Role;
use JuniorFontenele\LaravelPermission\Support\PermissionManager;
use JuniorFontenele\LaravelPermission\Support\TableNames;

it('can revoke a permission from a role', function () {
    /** @var PermissionManager $pm */
    $pm = app(PermissionManager::class);

    $pm->createPermission('companies.edit.all');

    $roleId = $pm->createRole('editor');

    /** @var Role $role */
    $role = Role::query()->findOrFail($roleId);

    $role->givePermissionTo('companies.edit.all');

    expect(DB::table(TableNames::roleHasPermissions())
        ->where('role_id', (int) $role->getKey())
        ->count())->toBe(1);

    $role->revokePermissionTo('companies.edit.all');

    expect(DB::table(TableNames::roleHasPermissions())
        ->where('role_id', (int) $role->getKey())
        ->count())->toBe(0);
});
