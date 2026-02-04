<?php

declare(strict_types = 1);

use JuniorFontenele\LaravelPermission\Facades\Permission;
use JuniorFontenele\LaravelPermission\Support\PermissionManager;
use Workbench\App\Models\User;

beforeEach(function () {
    /** @var PermissionManager $pm */
    $pm = app(PermissionManager::class);

    $pm->createPermission('companies.edit.all');
    $pm->createPermission('companies.edit.self');
    $pm->createPermission('companies.edit.attached');
});

it('authorizes all scope when user has permission', function () {
    $user = User::query()->create([
        'name' => 'U',
        'email' => 'u@example.com',
        'password' => bcrypt('password'),
    ]);

    $user->givePermissionTo('companies.edit.all');

    expect($user->can('companies.edit.all'))->toBeTrue();
});

it('authorizes self scope using created_by by default', function () {
    $user = User::query()->create([
        'name' => 'U',
        'email' => 'u2@example.com',
        'password' => bcrypt('password'),
    ]);

    $company = Workbench\App\Models\Company::query()->create([
        'name' => 'C1',
        'created_by' => $user->id,
    ]);

    $user->givePermissionTo('companies.edit.self');

    expect($user->can('companies.edit.self', $company))->toBeTrue();
});

it('authorizes attached scope when attachment exists', function () {
    $user = User::query()->create([
        'name' => 'U',
        'email' => 'u3@example.com',
        'password' => bcrypt('password'),
    ]);

    $company = Workbench\App\Models\Company::query()->create([
        'name' => 'C2',
        'created_by' => 999,
    ]);

    $user->givePermissionTo('companies.edit.attached');

    Permission::attach($user, 'companies.edit.attached', $company);

    expect($user->can('companies.edit.attached', $company))->toBeTrue();
});
