<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use JuniorFontenele\LaravelPermission\Models\Concerns\UsesPermissionTables;
use JuniorFontenele\LaravelPermission\Support\PermissionConfig;

class Permission extends Model
{
    use UsesPermissionTables;

    protected static string $permissionTableKey = 'permissions';

    protected $fillable = [
        'name',
        'guard_name',
    ];

    public function roles(): BelongsToMany
    {
        $roleClass = PermissionConfig::modelClass('role');

        return $this->belongsToMany($roleClass, PermissionConfig::table('role_has_permissions'));
    }
}
