<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\DB;
use JuniorFontenele\LaravelPermission\Support\PermissionManager;
use JuniorFontenele\LaravelPermission\Support\TableNames;
use Workbench\App\Models\User;

beforeEach(function () {
    /** @var PermissionManager $pm */
    $pm = app(PermissionManager::class);

    $pm->createPermission('companies.edit.all');
    $pm->createRole('editor', tenantId: 123);
    $pm->createRole('viewer', tenantId: 123);
});

it('defaults tenant_id using tenant_resolver for assignRole/removeRole', function () {
    config()->set('permission.tenant_resolver', fn () => 123);

    $user = User::query()->create([
        'name' => 'U',
        'email' => 'tenant-role@example.com',
        'password' => bcrypt('password'),
    ]);

    $user->assignRole('editor');

    $pivot = DB::table(TableNames::modelHasRoles())
        ->where('model_type', $user::class)
        ->where('model_id', $user->getKey())
        ->first();

    expect($pivot)->not->toBeNull();
    expect((int) $pivot->tenant_id)->toBe(123);

    $user->removeRole('editor');

    $exists = DB::table(TableNames::modelHasRoles())
        ->where('model_type', $user::class)
        ->where('model_id', $user->getKey())
        ->exists();

    expect($exists)->toBeFalse();
});

it('defaults tenant_id using tenant_resolver for givePermissionTo/revokePermissionTo', function () {
    config()->set('permission.tenant_resolver', fn () => 123);

    $user = User::query()->create([
        'name' => 'U',
        'email' => 'tenant-perm@example.com',
        'password' => bcrypt('password'),
    ]);

    $user->givePermissionTo('companies.edit.all');

    $pivot = DB::table(TableNames::modelHasPermissions())
        ->where('model_type', $user::class)
        ->where('model_id', $user->getKey())
        ->first();

    expect($pivot)->not->toBeNull();
    expect((int) $pivot->tenant_id)->toBe(123);

    $user->revokePermissionTo('companies.edit.all');

    $exists = DB::table(TableNames::modelHasPermissions())
        ->where('model_type', $user::class)
        ->where('model_id', $user->getKey())
        ->exists();

    expect($exists)->toBeFalse();
});

it('uses wherePivotNull semantics when tenant_id is null', function () {
    // single-tenant mode
    config()->set('permission.tenant_resolver', null);

    $user = User::query()->create([
        'name' => 'U',
        'email' => 'single-tenant@example.com',
        'password' => bcrypt('password'),
    ]);

    app(PermissionManager::class)->createRole('editor-st', tenantId: null);

    $user->assignRole('editor-st', tenantId: null);

    $pivot = DB::table(TableNames::modelHasRoles())
        ->join(TableNames::roles(), TableNames::roles() . '.id', '=', TableNames::modelHasRoles() . '.role_id')
        ->where(TableNames::modelHasRoles() . '.model_type', $user::class)
        ->where(TableNames::modelHasRoles() . '.model_id', $user->getKey())
        ->where(TableNames::roles() . '.name', 'editor-st')
        ->first();

    expect($pivot)->not->toBeNull();
    expect($pivot->tenant_id)->toBeNull();

    expect($user->hasRole('editor-st', tenantId: null))->toBeTrue();
});

it('defaults tenant_id using tenant_resolver for sync* APIs', function () {
    config()->set('permission.tenant_resolver', fn () => 123);

    $user = User::query()->create([
        'name' => 'U',
        'email' => 'tenant-sync@example.com',
        'password' => bcrypt('password'),
    ]);

    $user->syncRoles(['editor']);

    $tenantRoleCount = DB::table(TableNames::modelHasRoles())
        ->where('model_type', $user::class)
        ->where('model_id', $user->getKey())
        ->where('tenant_id', 123)
        ->count();

    expect($tenantRoleCount)->toBe(1);

    $user->syncRoles(['viewer']);

    $tenantRoleNames = DB::table(TableNames::modelHasRoles())
        ->join(TableNames::roles(), TableNames::roles() . '.id', '=', TableNames::modelHasRoles() . '.role_id')
        ->where(TableNames::modelHasRoles() . '.model_type', $user::class)
        ->where(TableNames::modelHasRoles() . '.model_id', $user->getKey())
        ->where(TableNames::modelHasRoles() . '.tenant_id', 123)
        ->pluck(TableNames::roles() . '.name')
        ->all();

    expect($tenantRoleNames)->toBe(['viewer']);

    $user->syncPermissions(['companies.edit.all']);

    $tenantPermCount = DB::table(TableNames::modelHasPermissions())
        ->where('model_type', $user::class)
        ->where('model_id', $user->getKey())
        ->where('tenant_id', 123)
        ->count();

    expect($tenantPermCount)->toBe(1);
});
