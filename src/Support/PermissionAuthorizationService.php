<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use JuniorFontenele\LaravelPermission\Contracts\AbilityParser;
use JuniorFontenele\LaravelPermission\Contracts\AuthorizationService;
use JuniorFontenele\LaravelPermission\Evaluators\AllScopeEvaluator;
use JuniorFontenele\LaravelPermission\Evaluators\AttachedScopeEvaluator;
use JuniorFontenele\LaravelPermission\Evaluators\SelfScopeEvaluator;
use JuniorFontenele\LaravelPermission\Resolvers\SelfResolver;
use JuniorFontenele\LaravelPermission\Resolvers\TenantResolver;

final class PermissionAuthorizationService implements AuthorizationService
{
    public function __construct(
        private AbilityParser $abilityParser,
        private TenantResolver $tenantResolver,
        private SelfResolver $selfResolver,
        private AllScopeEvaluator $all,
        private SelfScopeEvaluator $self,
        private AttachedScopeEvaluator $attached,
    ) {
    }

    public function check(Authenticatable $user, string $ability, array $arguments = []): ?bool
    {
        $parsed = $this->abilityParser->parse($ability);

        if (! $parsed) {
            return null;
        }

        $tenantId = $this->tenantResolver->resolveTenantId();

        // We treat the ability string itself as the permission name.
        $permissionName = $ability;

        if ($parsed->scope === 'all') {
            return $this->all->evaluate($user, $permissionName, $tenantId) ? true : null;
        }

        // option A: resource must be a model
        $resource = $arguments[0] ?? null;

        if (! is_object($resource)) {
            return null;
        }

        return match ($parsed->scope) {
            'self' => $this->self->evaluate($user, $permissionName, $resource, $tenantId, $this->selfResolver) ? true : null,
            'attached' => $this->attached->evaluate($user, $permissionName, $resource, $tenantId) ? true : null,
            default => null,
        };
    }
}
