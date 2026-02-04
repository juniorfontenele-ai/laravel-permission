<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Database\Eloquent\Model;

final class RoleStore
{
    public function findOrCreate(string $name, ?int $tenantId = null, string $guardName = 'web'): Model
    {
        $class = PermissionConfig::modelClass('role');

        $attributes = [
            'name' => $name,
            'guard_name' => $guardName,
        ];

        if (PermissionConfig::tenancyEnabled()) {
            $attributes[PermissionConfig::tenantColumn()] = $tenantId;
        }

        return $class::query()->firstOrCreate($attributes);
    }

    public function findByName(string $name, ?int $tenantId = null, string $guardName = 'web'): ?Model
    {
        $class = PermissionConfig::modelClass('role');

        return $class::query()
            ->when(
                PermissionConfig::tenancyEnabled(),
                fn ($q) => $q->where(PermissionConfig::tenantColumn(), $tenantId),
            )
            ->where('name', $name)
            ->where('guard_name', $guardName)
            ->first();
    }
}
