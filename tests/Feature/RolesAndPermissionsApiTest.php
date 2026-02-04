<?php

declare(strict_types = 1);

use JuniorFontenele\LaravelPermission\Facades\Permission;
use JuniorFontenele\LaravelPermission\Support\PermissionManager;
use Workbench\App\Models\Company;
use Workbench\App\Models\User;

beforeEach(function () {
    /** @var PermissionManager $pm */
    $pm = app(PermissionManager::class);

    $pm->createPermission('companies.edit.all');
    $pm->createPermission('companies.edit.self');
    $pm->createPermission('companies.edit.attached');
});

it('assigns roles and checks permissions through roles', function () {
    $user = User::query()->create([
        'name' => 'U',
        'email' => 'role@example.com',
        'password' => bcrypt('password'),
    ]);

    $roleId = app(PermissionManager::class)->createRole('editor');

    /** @var JuniorFontenele\LaravelPermission\Models\Role $role */
    $role = JuniorFontenele\LaravelPermission\Models\Role::query()->findOrFail($roleId);
    $role->givePermissionTo('companies.edit.all');

    $user->assignRole('editor');

    expect($user->can('companies.edit.all'))->toBeTrue();
});

it('syncs permissions and roles', function () {
    $user = User::query()->create([
        'name' => 'U2',
        'email' => 'sync@example.com',
        'password' => bcrypt('password'),
    ]);

    $user->syncPermissions(['companies.edit.self']);

    $company = Company::query()->create([
        'name' => 'C1',
        'created_by' => $user->id,
    ]);

    expect($user->can('companies.edit.self', $company))->toBeTrue();

    $user->syncPermissions(['companies.edit.attached']);

    Permission::attach($user, 'companies.edit.attached', $company);

    expect($user->can('companies.edit.attached', $company))->toBeTrue();
    expect($user->can('companies.edit.self', $company))->toBeFalse();
});
