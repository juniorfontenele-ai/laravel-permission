<?php

declare(strict_types = 1);

return [
    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    | You may override the Eloquent models used by the package.
    */
    'models' => [
        'permission' => JuniorFontenele\LaravelPermission\Models\Permission::class,
        'role' => JuniorFontenele\LaravelPermission\Models\Role::class,
        'attachment' => JuniorFontenele\LaravelPermission\Models\PermissionAttachment::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Table names
    |--------------------------------------------------------------------------
    | You may override any table name.
    */
    'tables' => [
        'permissions' => 'permissions',
        'roles' => 'roles',
        'role_has_permissions' => 'role_has_permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'permission_attachments' => 'permission_attachments',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenancy
    |--------------------------------------------------------------------------
    | Feature flag for multi-tenancy support.
    |
    | When enabled, the package will:
    | - create a tenant column on relevant tables (migrations)
    | - scope role/permission assignments by the resolved tenant id
    |
    | When disabled, the package ignores tenant id entirely and the migrations
    | will NOT create the tenant column.
    */
    'tenancy' => [
        'enabled' => true,
        'column' => 'tenant_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant resolver
    |--------------------------------------------------------------------------
    | Return current tenant id (bigint) or null.
    |
    | Note: this is only used when tenancy.enabled = true.
    */
    'tenant_resolver' => null, // fn(): int|null

    /*
    |--------------------------------------------------------------------------
    | Self resolver
    |--------------------------------------------------------------------------
    | Determines whether a given resource model should be considered "self"
    | for the current user.
    |
    | Default: compares $model->created_by with $user->getAuthIdentifier().
    */
    'self_resolver' => null, // fn(\Illuminate\Contracts\Auth\Authenticatable $user, object $resource): bool

    /*
    |--------------------------------------------------------------------------
    | Attachment resource type map
    |--------------------------------------------------------------------------
    | Map resource keys (like "companies") to model classes.
    | Used when recording attachments via helper APIs.
    */
    'resources' => [
        // 'companies' => App\Models\Company::class,
        // 'projects' => App\Models\Project::class,
    ],
];
