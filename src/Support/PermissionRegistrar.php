<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Support\Facades\DB;

final class PermissionRegistrar
{
    public function __construct(
        private PermissionStore $permissions,
        private RoleStore $roles,
    ) {
    }

    public function givePermissionToRole(int $roleId, int $permissionId): void
    {
        DB::table(TableNames::roleHasPermissions())
            ->updateOrInsert([
                'permission_id' => $permissionId,
                'role_id' => $roleId,
            ], []);
    }

    public function syncRolePermissions(int $roleId, array $permissionIds): void
    {
        $table = TableNames::roleHasPermissions();

        DB::table($table)->where('role_id', $roleId)->delete();

        foreach (array_unique($permissionIds) as $permissionId) {
            DB::table($table)->insert([
                'permission_id' => (int) $permissionId,
                'role_id' => (int) $roleId,
            ]);
        }
    }

    public function assignRoleToModel(int $roleId, string $modelType, int $modelId, ?int $tenantId = null): void
    {
        DB::table(TableNames::modelHasRoles())
            ->updateOrInsert([
                'role_id' => $roleId,
                'tenant_id' => $tenantId,
                'model_type' => $modelType,
                'model_id' => $modelId,
            ], []);
    }

    public function removeRoleFromModel(int $roleId, string $modelType, int $modelId, ?int $tenantId = null): void
    {
        DB::table(TableNames::modelHasRoles())
            ->where('role_id', $roleId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId), fn ($q) => $q->whereNull('tenant_id'))
            ->delete();
    }

    public function syncModelRoles(string $modelType, int $modelId, array $roleIds, ?int $tenantId = null): void
    {
        $table = TableNames::modelHasRoles();

        DB::table($table)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId), fn ($q) => $q->whereNull('tenant_id'))
            ->delete();

        foreach (array_unique($roleIds) as $roleId) {
            DB::table($table)->insert([
                'role_id' => (int) $roleId,
                'tenant_id' => $tenantId,
                'model_type' => $modelType,
                'model_id' => $modelId,
            ]);
        }
    }

    public function givePermissionToModel(int $permissionId, string $modelType, int $modelId, ?int $tenantId = null): void
    {
        DB::table(TableNames::modelHasPermissions())
            ->updateOrInsert([
                'permission_id' => $permissionId,
                'tenant_id' => $tenantId,
                'model_type' => $modelType,
                'model_id' => $modelId,
            ], []);
    }

    public function revokePermissionFromModel(int $permissionId, string $modelType, int $modelId, ?int $tenantId = null): void
    {
        DB::table(TableNames::modelHasPermissions())
            ->where('permission_id', $permissionId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId), fn ($q) => $q->whereNull('tenant_id'))
            ->delete();
    }

    public function syncModelPermissions(string $modelType, int $modelId, array $permissionIds, ?int $tenantId = null): void
    {
        $table = TableNames::modelHasPermissions();

        DB::table($table)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId), fn ($q) => $q->whereNull('tenant_id'))
            ->delete();

        foreach (array_unique($permissionIds) as $permissionId) {
            DB::table($table)->insert([
                'permission_id' => (int) $permissionId,
                'tenant_id' => $tenantId,
                'model_type' => $modelType,
                'model_id' => $modelId,
            ]);
        }
    }
}
