<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use JuniorFontenele\LaravelPermission\Support\PermissionConfig;

trait HasRoles
{
    public function roles(): MorphToMany
    {
        $roleClass = PermissionConfig::modelClass('role');

        return $this->morphToMany(
            $roleClass,
            'model',
            PermissionConfig::table('model_has_roles'),
            'model_id',
            'role_id'
        )->withPivot('tenant_id');
    }
}
