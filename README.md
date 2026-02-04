# juniorfontenele/laravel-permission

RBAC para Laravel no estilo **spatie/laravel-permission**, mas com suporte nativo a **scopes** por ability:

- `*.all` (acesso total)
- `*.self` (acesso somente ao próprio recurso, via resolver)
- `*.attached` (acesso somente aos recursos anexados/permitidos explicitamente)

> **Modelo 1:1:** a *ability string* do Gate é **o nome da permission** (ex.: `companies.edit.attached` == `permissions.name`).

## Instalação

```bash
composer require juniorfontenele/laravel-permission
```

Publique config (opcional):

```bash
php artisan vendor:publish --tag="permission-config"
```

Rode as migrations:

```bash
php artisan migrate
```

## Configuração

`config/permission.php`:

- `models.permission|role|attachment`: trocar os models Eloquent
- `tables.*`: trocar nome das tabelas
- `tenancy.enabled`: feature flag de multi-tenancy (default: `true`)
- `tenancy.column`: nome da coluna de tenant (default: `tenant_id`)
- `tenant_resolver`: callback para pegar o tenant id atual (nullable)
- `self_resolver`: callback para definir o que é "self"

### Default self
Se você não definir `self_resolver`, o pacote usa a convenção:

- `resource->created_by == user->id`

## Uso

### 1) No seu User
Adicione o trait:

```php
use JuniorFontenele\LaravelPermission\Traits\InteractsWithPermissions;

class User extends Authenticatable
{
    use InteractsWithPermissions;
}
```

### 2) Criar e atribuir permissions
As permissions são strings únicas (campo `permissions.name` é **unique**):

```php
$user->givePermissionTo('companies.edit.all');
```

### 3) Roles
```php
$user->assignRole('editor');
$user->syncRoles(['editor', 'viewer']);
$user->removeRole('viewer');
```

Permissão via Role:

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

- `all`: checa só RBAC
- `self`: RBAC + self_resolver
- `attached`: RBAC + registro em `permission_attachments`

### 5) Attachments (attached scope)

```php
use JuniorFontenele\LaravelPermission\Facades\Permission;

Permission::attach($user, 'companies.edit.attached', $company);

$user->can('companies.edit.attached', $company); // true
```

## Multi-tenancy

O suporte a multi-tenancy é um **feature flag**:

- `permission.tenancy.enabled = true`: cria/usa coluna de tenant nas tabelas (migrations + queries)
- `permission.tenancy.enabled = false`: ignora tenant completamente e **não** cria a coluna

A coluna é configurável via `permission.tenancy.column` (default: `tenant_id`).

Para ativar scoping por tenant, defina `permission.tenant_resolver` no config (ou passe explicitamente `tenantId` nas APIs).

## Testes

```bash
composer test
```
