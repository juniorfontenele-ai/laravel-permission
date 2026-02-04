<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

final class UserPermissionChecker
{
    public function __construct(private PermissionStore $permissions)
    {
    }

    public function permissionIdByName(string $permissionName): ?int
    {
        $permission = $this->permissions->findByName($permissionName);

        return $permission?->getKey() ? (int) $permission->getKey() : null;
    }

    public function userHasPermissionName(Authenticatable $user, string $permissionName, ?int $tenantId): bool
    {
        $permissionId = $this->permissionIdByName($permissionName);

        if (! $permissionId) {
            return false;
        }

        return $this->userHasPermissionId($user, $permissionId, $tenantId);
    }

    public function userHasPermissionId(Authenticatable $user, int $permissionId, ?int $tenantId): bool
    {
        $userType = $user::class;
        $userId = (int) $user->getAuthIdentifier();

        $modelHasPermissions = PermissionConfig::table('model_has_permissions');
        $modelHasRoles = PermissionConfig::table('model_has_roles');
        $roleHasPermissions = PermissionConfig::table('role_has_permissions');

        // Direct permission
        $directQuery = DB::table($modelHasPermissions)
            ->where('permission_id', $permissionId)
            ->where('model_type', $userType)
            ->where('model_id', $userId);

        if (PermissionConfig::tenancyEnabled()) {
            $tenantColumn = PermissionConfig::tenantColumn();

            $directQuery->when(
                $tenantId !== null,
                fn ($q) => $q->where($tenantColumn, $tenantId),
                fn ($q) => $q->whereNull($tenantColumn)
            );
        }

        if ($directQuery->exists()) {
            return true;
        }

        // Permission via roles
        $roleQuery = DB::table($modelHasRoles)
            ->join($roleHasPermissions, "$modelHasRoles.role_id", '=', "$roleHasPermissions.role_id")
            ->where("$roleHasPermissions.permission_id", $permissionId)
            ->where("$modelHasRoles.model_type", $userType)
            ->where("$modelHasRoles.model_id", $userId);

        if (PermissionConfig::tenancyEnabled()) {
            $tenantColumn = PermissionConfig::tenantColumn();

            $roleQuery->when(
                $tenantId !== null,
                fn ($q) => $q->where("$modelHasRoles.$tenantColumn", $tenantId),
                fn ($q) => $q->whereNull("$modelHasRoles.$tenantColumn")
            );
        }

        return $roleQuery->exists();
    }
}
