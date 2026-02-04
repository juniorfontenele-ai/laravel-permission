<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Evaluators;

use Illuminate\Contracts\Auth\Authenticatable;
use JuniorFontenele\LaravelPermission\Support\UserPermissionChecker;

final class AllScopeEvaluator
{
    public function __construct(private UserPermissionChecker $checker)
    {
    }

    public function evaluate(Authenticatable $user, string $permissionName, ?int $tenantId): bool
    {
        return $this->checker->userHasPermissionName($user, $permissionName, $tenantId);
    }
}
