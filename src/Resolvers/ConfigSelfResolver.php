<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Resolvers;

use Illuminate\Contracts\Auth\Authenticatable;

final class ConfigSelfResolver implements SelfResolver
{
    public function isSelf(Authenticatable $user, object $resource): bool
    {
        /** @var callable|null $resolver */
        $resolver = config('permission.self_resolver');

        if ($resolver) {
            return (bool) $resolver($user, $resource);
        }

        // default convention: created_by
        if (! isset($resource->created_by)) {
            return false;
        }

        return (string) $resource->created_by === (string) $user->getAuthIdentifier();
    }
}
