<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use JuniorFontenele\LaravelPermission\Contracts\AuthorizationService as AuthorizationServiceContract;
use JuniorFontenele\LaravelPermission\Evaluators\AllScopeEvaluator;
use JuniorFontenele\LaravelPermission\Evaluators\AttachedScopeEvaluator;
use JuniorFontenele\LaravelPermission\Evaluators\SelfScopeEvaluator;
use JuniorFontenele\LaravelPermission\Resolvers\SelfResolver;
use JuniorFontenele\LaravelPermission\Resolvers\TenantResolver;

class PermissionManager
{
    public function __construct(
        private PermissionStore $permissions,
        private RoleStore $roles,
    ) {
    }

    public function authorizationService(): AuthorizationServiceContract
    {
        $checker = new UserPermissionChecker($this->permissions);

        return new PermissionAuthorizationService(
            abilityParser: app(\JuniorFontenele\LaravelPermission\Contracts\AbilityParser::class),
            tenantResolver: app(TenantResolver::class),
            selfResolver: app(SelfResolver::class),
            all: new AllScopeEvaluator($checker),
            self: new SelfScopeEvaluator($checker),
            attached: new AttachedScopeEvaluator($checker),
        );
    }

    // --- API (MVP) ------------------------------------------------------

    public function createPermission(string $name, string $guardName = 'web'): int
    {
        return (int) $this->permissions->findOrCreate($name, $guardName)->getKey();
    }

    public function createRole(string $name, ?int $tenantId = null, string $guardName = 'web'): int
    {
        return (int) $this->roles->findOrCreate($name, $tenantId, $guardName)->getKey();
    }

    /**
     * Records an attachment for an already-existing permission (usually *.attached).
     * Option A: resource must be a model instance.
     */
    public function attach(Authenticatable $user, string $permissionName, object $resource, ?int $tenantId = null): void
    {
        $permissionId = $this->permissions->findByName($permissionName)?->getKey();

        if (! $permissionId) {
            // optional: auto-create. For now, create to reduce friction.
            $permissionId = $this->createPermission($permissionName);
        }

        PermissionAttachment::attach($user, (int) $permissionId, $resource, $tenantId);
    }
}
