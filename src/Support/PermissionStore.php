<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Database\Eloquent\Model;

final class PermissionStore
{
    /** @return \JuniorFontenele\LaravelPermission\Models\Permission */
    private function model(): Model
    {
        $class = PermissionConfig::modelClass('permission');

        return new $class();
    }

    public function findOrCreate(string $name, string $guardName = 'web'): Model
    {
        $class = PermissionConfig::modelClass('permission');

        return $class::query()->firstOrCreate([
            'name' => $name,
        ], [
            'guard_name' => $guardName,
        ]);
    }

    public function findByName(string $name): ?Model
    {
        $class = PermissionConfig::modelClass('permission');

        return $class::query()->where('name', $name)->first();
    }
}
