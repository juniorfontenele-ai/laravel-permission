<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use JuniorFontenele\LaravelPermission\Support\PermissionConfig;

trait HasPermissions
{
    public function permissions(): MorphToMany
    {
        $permissionClass = PermissionConfig::modelClass('permission');

        $relation = $this->morphToMany(
            $permissionClass,
            'model',
            PermissionConfig::table('model_has_permissions'),
            'model_id',
            'permission_id'
        );

        if (PermissionConfig::tenancyEnabled()) {
            $relation->withPivot(PermissionConfig::tenantColumn());
        }

        return $relation;
    }
}
