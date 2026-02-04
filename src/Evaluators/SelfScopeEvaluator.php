<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Evaluators;

use Illuminate\Contracts\Auth\Authenticatable;
use JuniorFontenele\LaravelPermission\Resolvers\SelfResolver;
use JuniorFontenele\LaravelPermission\Support\UserPermissionChecker;

final class SelfScopeEvaluator
{
    public function __construct(private UserPermissionChecker $checker)
    {
    }

    public function evaluate(
        Authenticatable $user,
        string $permissionName,
        object $resource,
        ?int $tenantId,
        SelfResolver $selfResolver,
    ): bool {
        if (! $this->checker->userHasPermissionName($user, $permissionName, $tenantId)) {
            return false;
        }

        return $selfResolver->isSelf($user, $resource);
    }
}
