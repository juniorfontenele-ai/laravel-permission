<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Resolvers;

use Illuminate\Contracts\Auth\Authenticatable;

interface SelfResolver
{
    public function isSelf(Authenticatable $user, object $resource): bool;
}
