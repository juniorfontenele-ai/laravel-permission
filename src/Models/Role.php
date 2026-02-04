<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use JuniorFontenele\LaravelPermission\Models\Concerns\UsesPermissionTables;
use JuniorFontenele\LaravelPermission\Support\PermissionConfig;

class Role extends Model
{
    use UsesPermissionTables;

    protected static string $permissionTableKey = 'roles';

    protected $fillable = [
        'tenant_id',
        'name',
        'guard_name',
    ];

    public function permissions(): BelongsToMany
    {
        $permissionClass = PermissionConfig::modelClass('permission');

        return $this->belongsToMany($permissionClass, PermissionConfig::table('role_has_permissions'));
    }

    public function givePermissionTo(string $permissionName, string $guardName = 'web'): void
    {
        $permissionClass = PermissionConfig::modelClass('permission');

        /** @var Permission $permission */
        $permission = $permissionClass::query()->firstOrCreate([
            'name' => $permissionName,
        ], [
            'guard_name' => $guardName,
        ]);

        DB::table(PermissionConfig::table('role_has_permissions'))
            ->updateOrInsert([
                'permission_id' => (int) $permission->getKey(),
                'role_id' => (int) $this->getKey(),
            ], []);
    }

    /** @param array<int, string> $permissionNames */
    public function syncPermissions(array $permissionNames, string $guardName = 'web'): void
    {
        $permissionClass = PermissionConfig::modelClass('permission');

        $permissionIds = [];

        foreach ($permissionNames as $permissionName) {
            /** @var Permission $permission */
            $permission = $permissionClass::query()->firstOrCreate([
                'name' => $permissionName,
            ], [
                'guard_name' => $guardName,
            ]);

            $permissionIds[] = (int) $permission->getKey();
        }

        $table = PermissionConfig::table('role_has_permissions');

        DB::table($table)->where('role_id', (int) $this->getKey())->delete();

        foreach (array_unique($permissionIds) as $permissionId) {
            DB::table($table)->insert([
                'permission_id' => (int) $permissionId,
                'role_id' => (int) $this->getKey(),
            ]);
        }
    }

    public function revokePermissionTo(string $permissionName): void
    {
        $permissionClass = PermissionConfig::modelClass('permission');

        /** @var Permission|null $permission */
        $permission = $permissionClass::query()->where('name', $permissionName)->first();

        if (! $permission) {
            return;
        }

        DB::table(PermissionConfig::table('role_has_permissions'))
            ->where('permission_id', (int) $permission->getKey())
            ->where('role_id', (int) $this->getKey())
            ->delete();
    }
}
