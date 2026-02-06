# Laravel Permission

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jftecnologia/laravel-permission.svg?style=flat-square)](https://packagist.org/packages/jftecnologia/laravel-permission)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jftecnologia/laravel-permission/tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/jftecnologia/laravel-permission/actions?query=workflow%3Atests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jftecnologia/laravel-permission/fix-php-code-style.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/jftecnologia/laravel-permission/actions?query=workflow%3A"fix-php-code-style-issues"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/jftecnologia/laravel-permission.svg?style=flat-square)](https://packagist.org/packages/jftecnologia/laravel-permission)

RBAC for Laravel with scoped permissions (`all`, `self`, `attached`) and optional multi-tenancy support.

> **1:1 model:** the Gate ability string equals the permission name (e.g. `companies.edit.attached` == `permissions.name`).

## Features

- **Scoped permissions** with `all`, `self`, and `attached` scopes
- **Roles and permissions** with a familiar API
- **Attachment-based access** for fine-grained authorization
- **Optional multi-tenancy** via a feature flag
- **Configurable resolvers** for tenant and self resolution

## Installation

```bash
composer require jftecnologia/laravel-permission
```

Publish config (optional):

```bash
php artisan vendor:publish --tag="permission-config"
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

`config/permission.php`:

- `models.permission|role|attachment`: swap the Eloquent models
- `tables.*`: rename tables
- `tenancy.enabled`: feature flag for multi-tenancy (default: `true`)
- `tenancy.column`: tenant column name (default: `tenant_id`)
- `tenant_resolver`: callback to resolve the current tenant id (nullable)
- `self_resolver`: callback to define what "self" means

### Default self

If you don't define `self_resolver`, the package uses the convention:

- `resource->created_by == user->id`

## Usage

### 1) On your User model

Add the trait:

```php
use JuniorFontenele\LaravelPermission\Traits\InteractsWithPermissions;

class User extends Authenticatable
{
    use InteractsWithPermissions;
}
```

### 2) Create and assign permissions

Permissions are unique strings (`permissions.name` is **unique**):

```php
$user->givePermissionTo('companies.edit.all');
```

### 3) Roles

```php
$user->assignRole('editor');
$user->syncRoles(['editor', 'viewer']);
$user->removeRole('viewer');
```

Permission via role:

```php
$role = \JuniorFontenele\LaravelPermission\Models\Role::query()->firstOrCreate([
    'tenant_id' => null,
    'name' => 'editor',
    'guard_name' => 'web',
]);

$role->givePermissionTo('companies.edit.all');
```

### 4) Gate/Policies (scopes)

```php
$user->can('companies.edit.all');
$user->can('companies.edit.self', $company);
$user->can('companies.edit.attached', $company);
```

- `all`: checks RBAC only
- `self`: RBAC + `self_resolver`
- `attached`: RBAC + `permission_attachments` record

### 5) Attachments (attached scope)

```php
use JuniorFontenele\LaravelPermission\Facades\Permission;

Permission::attach($user, 'companies.edit.attached', $company);

$user->can('companies.edit.attached', $company); // true
```

## Multi-tenancy

Multi-tenancy support is a **feature flag**:

- `permission.tenancy.enabled = true`: creates/uses tenant column in tables (migrations + queries)
- `permission.tenancy.enabled = false`: ignores tenant entirely and **does not** create the column

The column is configurable via `permission.tenancy.column` (default: `tenant_id`).

To enable tenant scoping, define `permission.tenant_resolver` in config (or pass `tenantId` explicitly in APIs).

## Testing

```bash
composer test
```

## Credits

- [Junior Fontenele](https://github.com/juniorfontenele)

## License

MIT License. See [LICENSE.md](LICENSE.md) for details.
