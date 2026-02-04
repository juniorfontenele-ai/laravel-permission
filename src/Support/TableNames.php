<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

final class TableNames
{
    public static function permissions(): string
    {
        return PermissionConfig::table('permissions');
    }

    public static function roles(): string
    {
        return PermissionConfig::table('roles');
    }

    public static function roleHasPermissions(): string
    {
        return PermissionConfig::table('role_has_permissions');
    }

    public static function modelHasPermissions(): string
    {
        return PermissionConfig::table('model_has_permissions');
    }

    public static function modelHasRoles(): string
    {
        return PermissionConfig::table('model_has_roles');
    }

    public static function attachments(): string
    {
        return PermissionConfig::table('permission_attachments');
    }
}
