<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Database\Eloquent\Model;

final class PermissionStore
{
    /** @return class-string<Model> */
    private function modelClass(): string
    {
        /** @var class-string<Model> $class */
        $class = PermissionConfig::modelClass('permission');

        return $class;
    }

    public function findOrCreate(string $name, string $guardName = 'web'): Model
    {
        $class = $this->modelClass();

        return $class::query()->firstOrCreate([
            'name' => $name,
        ], [
            'guard_name' => $guardName,
        ]);
    }

    public function findByName(string $name): ?Model
    {
        $class = $this->modelClass();

        return $class::query()->where('name', $name)->first();
    }
}
