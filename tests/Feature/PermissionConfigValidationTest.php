<?php

declare(strict_types = 1);

use Illuminate\Database\Eloquent\Model;
use JuniorFontenele\LaravelPermission\Support\PermissionConfig;

it('throws when a table name contains invalid characters', function () {
    config()->set('permission.tables.invalid', 'roles;drop');

    expect(fn () => PermissionConfig::table('invalid'))
        ->toThrow(InvalidArgumentException::class);
});

it('throws when a configured model class does not exist', function () {
    config()->set('permission.models.role', 'App\\Models\\DoesNotExist');

    expect(fn () => PermissionConfig::modelClass('role'))
        ->toThrow(InvalidArgumentException::class, 'does not exist');
});

it('throws when a configured model class is not an Eloquent model', function () {
    config()->set('permission.models.role', stdClass::class);

    expect(fn () => PermissionConfig::modelClass('role'))
        ->toThrow(InvalidArgumentException::class, 'must extend ' . Model::class);
});
