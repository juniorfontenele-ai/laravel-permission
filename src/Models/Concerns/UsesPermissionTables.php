<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Models\Concerns;

use JuniorFontenele\LaravelPermission\Support\PermissionConfig;

trait UsesPermissionTables
{
    // Models using this trait must define: protected static string $permissionTableKey;

    public function getTable(): string
    {
        return PermissionConfig::table(static::$permissionTableKey);
    }
}
