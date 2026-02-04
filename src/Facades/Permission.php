<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void attach(\Illuminate\Contracts\Auth\Authenticatable $user, string $permissionName, object $resource)
 */
final class Permission extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JuniorFontenele\LaravelPermission\Permission::class;
    }
}
