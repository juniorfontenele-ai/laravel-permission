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

        $relation = $this->morphToMany(
            $roleClass,
            'model',
            PermissionConfig::table('model_has_roles'),
            'model_id',
            'role_id'
        );

        if (PermissionConfig::tenancyEnabled()) {
            $relation->withPivot(PermissionConfig::tenantColumn());
        }

        return $relation;
    }
}
