<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Database\Eloquent\Model;

final class RoleStore
{
    public function findOrCreate(string $name, ?int $tenantId = null, string $guardName = 'web'): Model
    {
        $class = PermissionConfig::modelClass('role');

        return $class::query()->firstOrCreate([
            'tenant_id' => $tenantId,
            'name' => $name,
            'guard_name' => $guardName,
        ]);
    }

    public function findByName(string $name, ?int $tenantId = null, string $guardName = 'web'): ?Model
    {
        $class = PermissionConfig::modelClass('role');

        return $class::query()
            ->where('tenant_id', $tenantId)
            ->where('name', $name)
            ->where('guard_name', $guardName)
            ->first();
    }
}
