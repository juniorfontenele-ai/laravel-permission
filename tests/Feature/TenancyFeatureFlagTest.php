<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JuniorFontenele\LaravelPermission\Support\PermissionManager;
use JuniorFontenele\LaravelPermission\Support\TableNames;
use Workbench\App\Models\User;

it('does not create tenant columns when tenancy is disabled', function () {
    config()->set('permission.tenancy.enabled', false);

    Artisan::call('migrate:fresh');

    expect(Schema::hasColumn(TableNames::roles(), 'tenant_id'))->toBeFalse();
    expect(Schema::hasColumn(TableNames::modelHasRoles(), 'tenant_id'))->toBeFalse();
    expect(Schema::hasColumn(TableNames::modelHasPermissions(), 'tenant_id'))->toBeFalse();
    expect(Schema::hasColumn(TableNames::attachments(), 'tenant_id'))->toBeFalse();
});

it('ignores tenant scoping when tenancy is disabled', function () {
    config()->set('permission.tenancy.enabled', false);
    config()->set('permission.tenant_resolver', fn () => 123);

    Artisan::call('migrate:fresh');

    /** @var PermissionManager $pm */
    $pm = app(PermissionManager::class);

    $pm->createPermission('companies.edit.all');
    $pm->createRole('editor', tenantId: 123);

    $user = User::query()->create([
        'name' => 'U',
        'email' => 'tenancy-disabled@example.com',
        'password' => bcrypt('password'),
    ]);

    // Even if a tenant id is provided/resolved, it must be ignored.
    $user->assignRole('editor', tenantId: 123);
    $user->givePermissionTo('companies.edit.all', tenantId: 123);

    $rolePivot = DB::table(TableNames::modelHasRoles())
        ->where('model_type', $user::class)
        ->where('model_id', $user->getKey())
        ->first();

    $permPivot = DB::table(TableNames::modelHasPermissions())
        ->where('model_type', $user::class)
        ->where('model_id', $user->getKey())
        ->first();

    expect($rolePivot)->not->toBeNull();
    expect(array_key_exists('tenant_id', (array) $rolePivot))->toBeFalse();

    expect($permPivot)->not->toBeNull();
    expect(array_key_exists('tenant_id', (array) $permPivot))->toBeFalse();

    expect($user->hasRole('editor'))->toBeTrue();
    expect($user->hasPermissionTo('companies.edit.all'))->toBeTrue();
});

it('supports a custom tenant column when tenancy is enabled', function () {
    config()->set('permission.tenancy.enabled', true);
    config()->set('permission.tenancy.column', 'company_id');
    config()->set('permission.tenant_resolver', fn () => 321);

    Artisan::call('migrate:fresh');

    expect(Schema::hasColumn(TableNames::roles(), 'company_id'))->toBeTrue();
    expect(Schema::hasColumn(TableNames::modelHasRoles(), 'company_id'))->toBeTrue();

    /** @var PermissionManager $pm */
    $pm = app(PermissionManager::class);

    $pm->createRole('editor', tenantId: 321);

    $user = User::query()->create([
        'name' => 'U',
        'email' => 'tenancy-custom-column@example.com',
        'password' => bcrypt('password'),
    ]);

    $user->assignRole('editor');

    $pivot = DB::table(TableNames::modelHasRoles())
        ->where('model_type', $user::class)
        ->where('model_id', $user->getKey())
        ->first();

    expect($pivot)->not->toBeNull();
    expect((int) $pivot->company_id)->toBe(321);
});
