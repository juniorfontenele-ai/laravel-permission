<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class PermissionConfig
{
    public static function tenancyEnabled(): bool
    {
        return (bool) config('permission.tenancy.enabled', true);
    }

    public static function tenantColumn(): string
    {
        $column = config('permission.tenancy.column', 'tenant_id');

        if (! is_string($column)) {
            throw new InvalidArgumentException('Invalid permission tenancy column: expected string.');
        }

        self::assertValidIdentifier($column, 'tenancy.column');

        return $column;
    }

    public static function modelClass(string $key): string
    {
        $class = config("permission.models.$key");

        if (! is_string($class) || $class === '') {
            throw new InvalidArgumentException("Invalid permission model class for key [$key]: expected non-empty string.");
        }

        if (! class_exists($class)) {
            throw new InvalidArgumentException("Invalid permission model class for key [$key]: class [$class] does not exist.");
        }

        if (! is_subclass_of($class, Model::class)) {
            throw new InvalidArgumentException(
                "Invalid permission model class for key [$key]: class [$class] must extend " . Model::class . '.'
            );
        }

        return $class;
    }

    public static function table(string $key): string
    {
        $table = config("permission.tables.$key", $key);

        if (! is_string($table) || $table === '') {
            throw new InvalidArgumentException("Invalid permission table name for key [$key]: expected non-empty string.");
        }

        self::assertValidIdentifier($table, "tables.$key");

        return $table;
    }

    private static function assertValidIdentifier(string $value, string $configKey): void
    {
        if (! preg_match('/\A[A-Za-z0-9_]+\z/', $value)) {
            throw new InvalidArgumentException(
                "Invalid permission table/column name for config [$configKey]: [$value]. "
                . 'Only letters, numbers, and underscore are allowed.'
            );
        }
    }
}
