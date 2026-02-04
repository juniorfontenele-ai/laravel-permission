<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Evaluators;

use Illuminate\Contracts\Auth\Authenticatable;
use JuniorFontenele\LaravelPermission\Support\PermissionAttachment;
use JuniorFontenele\LaravelPermission\Support\UserPermissionChecker;

final class AttachedScopeEvaluator
{
    public function __construct(private UserPermissionChecker $checker)
    {
    }

    public function evaluate(
        Authenticatable $user,
        string $permissionName,
        object $resource,
        ?int $tenantId,
    ): bool {
        $permissionId = $this->checker->permissionIdByName($permissionName);

        if (! $permissionId) {
            return false;
        }

        if (! $this->checker->userHasPermissionId($user, $permissionId, $tenantId)) {
            return false;
        }

        return PermissionAttachment::exists($user, $permissionId, $resource, $tenantId);
    }
}
