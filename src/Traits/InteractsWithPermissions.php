<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Traits;

use JuniorFontenele\LaravelPermission\Support\PermissionRegistrar;
use JuniorFontenele\LaravelPermission\Support\PermissionStore;
use JuniorFontenele\LaravelPermission\Support\RoleStore;
use JuniorFontenele\LaravelPermission\Support\UserPermissionChecker;

trait InteractsWithPermissions
{
    use HasRoles;
    use HasPermissions;

    // --- Roles ----------------------------------------------------------

    public function assignRole(string $roleName, ?int $tenantId = null, string $guardName = 'web'): void
    {
        /** @var RoleStore $roles */
        $roles = app(RoleStore::class);
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        $role = $roles->findOrCreate($roleName, $tenantId, $guardName);

        $registrar->assignRoleToModel(
            roleId: (int) $role->getKey(),
            modelType: $this::class,
            modelId: (int) $this->getKey(),
            tenantId: $tenantId,
        );
    }

    public function removeRole(string $roleName, ?int $tenantId = null, string $guardName = 'web'): void
    {
        /** @var RoleStore $roles */
        $roles = app(RoleStore::class);
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        $role = $roles->findByName($roleName, $tenantId, $guardName);

        if (! $role) {
            return;
        }

        $registrar->removeRoleFromModel(
            roleId: (int) $role->getKey(),
            modelType: $this::class,
            modelId: (int) $this->getKey(),
            tenantId: $tenantId,
        );
    }

    /** @param array<int, string> $roleNames */
    public function syncRoles(array $roleNames, ?int $tenantId = null, string $guardName = 'web'): void
    {
        /** @var RoleStore $roles */
        $roles = app(RoleStore::class);
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        $roleIds = [];

        foreach ($roleNames as $roleName) {
            $roleIds[] = (int) $roles->findOrCreate($roleName, $tenantId, $guardName)->getKey();
        }

        $registrar->syncModelRoles(
            modelType: $this::class,
            modelId: (int) $this->getKey(),
            roleIds: $roleIds,
            tenantId: $tenantId,
        );
    }

    public function hasRole(string $roleName, ?int $tenantId = null, string $guardName = 'web'): bool
    {
        return $this->roles()
            ->where('name', $roleName)
            ->where('guard_name', $guardName)
            ->wherePivot('tenant_id', $tenantId)
            ->exists();
    }

    // --- Permissions ----------------------------------------------------

    public function givePermissionTo(string $permissionName, ?int $tenantId = null, string $guardName = 'web'): void
    {
        /** @var PermissionStore $permissions */
        $permissions = app(PermissionStore::class);
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        $permission = $permissions->findOrCreate($permissionName, $guardName);

        $registrar->givePermissionToModel(
            permissionId: (int) $permission->getKey(),
            modelType: $this::class,
            modelId: (int) $this->getKey(),
            tenantId: $tenantId,
        );
    }

    public function revokePermissionTo(string $permissionName, ?int $tenantId = null): void
    {
        /** @var PermissionStore $permissions */
        $permissions = app(PermissionStore::class);
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        $permission = $permissions->findByName($permissionName);

        if (! $permission) {
            return;
        }

        $registrar->revokePermissionFromModel(
            permissionId: (int) $permission->getKey(),
            modelType: $this::class,
            modelId: (int) $this->getKey(),
            tenantId: $tenantId,
        );
    }

    /** @param array<int, string> $permissionNames */
    public function syncPermissions(array $permissionNames, ?int $tenantId = null, string $guardName = 'web'): void
    {
        /** @var PermissionStore $permissions */
        $permissions = app(PermissionStore::class);
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        $permissionIds = [];

        foreach ($permissionNames as $permissionName) {
            $permissionIds[] = (int) $permissions->findOrCreate($permissionName, $guardName)->getKey();
        }

        $registrar->syncModelPermissions(
            modelType: $this::class,
            modelId: (int) $this->getKey(),
            permissionIds: $permissionIds,
            tenantId: $tenantId,
        );
    }

    public function hasPermissionTo(string $permissionName, ?int $tenantId = null): bool
    {
        $checker = new UserPermissionChecker(app(PermissionStore::class));

        return $checker->userHasPermissionName($this, $permissionName, $tenantId);
    }
}
