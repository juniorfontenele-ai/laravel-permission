<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface AuthorizationService
{
    /**
     * Returns:
     * - true when this package authorizes the ability
     * - null when this package does not apply (Laravel continues normal Gate flow)
     */
    public function check(Authenticatable $user, string $ability, array $arguments = []): ?bool;
}
