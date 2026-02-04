<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Support\Facades\DB;

final class PermissionRegistrar
{
    public function __construct()
    {
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
        $attributes = [
            'role_id' => $roleId,
            'model_type' => $modelType,
            'model_id' => $modelId,
        ];

        if (PermissionConfig::tenancyEnabled()) {
            $attributes[PermissionConfig::tenantColumn()] = $tenantId;
        }

        DB::table(TableNames::modelHasRoles())->updateOrInsert($attributes, []);
    }

    public function removeRoleFromModel(int $roleId, string $modelType, int $modelId, ?int $tenantId = null): void
    {
        $query = DB::table(TableNames::modelHasRoles())
            ->where('role_id', $roleId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId);

        if (PermissionConfig::tenancyEnabled()) {
            $tenantColumn = PermissionConfig::tenantColumn();

            $query->when(
                $tenantId !== null,
                fn ($q) => $q->where($tenantColumn, $tenantId),
                fn ($q) => $q->whereNull($tenantColumn)
            );
        }

        $query->delete();
    }

    public function syncModelRoles(string $modelType, int $modelId, array $roleIds, ?int $tenantId = null): void
    {
        $table = TableNames::modelHasRoles();

        $query = DB::table($table)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId);

        if (PermissionConfig::tenancyEnabled()) {
            $tenantColumn = PermissionConfig::tenantColumn();

            $query->when(
                $tenantId !== null,
                fn ($q) => $q->where($tenantColumn, $tenantId),
                fn ($q) => $q->whereNull($tenantColumn)
            );
        }

        $query->delete();

        foreach (array_unique($roleIds) as $roleId) {
            $attributes = [
                'role_id' => (int) $roleId,
                'model_type' => $modelType,
                'model_id' => $modelId,
            ];

            if (PermissionConfig::tenancyEnabled()) {
                $attributes[PermissionConfig::tenantColumn()] = $tenantId;
            }

            DB::table($table)->insert($attributes);
        }
    }

    public function givePermissionToModel(int $permissionId, string $modelType, int $modelId, ?int $tenantId = null): void
    {
        $attributes = [
            'permission_id' => $permissionId,
            'model_type' => $modelType,
            'model_id' => $modelId,
        ];

        if (PermissionConfig::tenancyEnabled()) {
            $attributes[PermissionConfig::tenantColumn()] = $tenantId;
        }

        DB::table(TableNames::modelHasPermissions())->updateOrInsert($attributes, []);
    }

    public function revokePermissionFromModel(int $permissionId, string $modelType, int $modelId, ?int $tenantId = null): void
    {
        $query = DB::table(TableNames::modelHasPermissions())
            ->where('permission_id', $permissionId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId);

        if (PermissionConfig::tenancyEnabled()) {
            $tenantColumn = PermissionConfig::tenantColumn();

            $query->when(
                $tenantId !== null,
                fn ($q) => $q->where($tenantColumn, $tenantId),
                fn ($q) => $q->whereNull($tenantColumn)
            );
        }

        $query->delete();
    }

    public function syncModelPermissions(string $modelType, int $modelId, array $permissionIds, ?int $tenantId = null): void
    {
        $table = TableNames::modelHasPermissions();

        $query = DB::table($table)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId);

        if (PermissionConfig::tenancyEnabled()) {
            $tenantColumn = PermissionConfig::tenantColumn();

            $query->when(
                $tenantId !== null,
                fn ($q) => $q->where($tenantColumn, $tenantId),
                fn ($q) => $q->whereNull($tenantColumn)
            );
        }

        $query->delete();

        foreach (array_unique($permissionIds) as $permissionId) {
            $attributes = [
                'permission_id' => (int) $permissionId,
                'model_type' => $modelType,
                'model_id' => $modelId,
            ];

            if (PermissionConfig::tenancyEnabled()) {
                $attributes[PermissionConfig::tenantColumn()] = $tenantId;
            }

            DB::table($table)->insert($attributes);
        }
    }
}
