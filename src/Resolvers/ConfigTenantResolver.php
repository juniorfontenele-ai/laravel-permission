<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Resolvers;

final class ConfigTenantResolver implements TenantResolver
{
    public function resolveTenantId(): ?int
    {
        if (! \JuniorFontenele\LaravelPermission\Support\PermissionConfig::tenancyEnabled()) {
            return null;
        }

        /** @var callable|null $resolver */
        $resolver = config('permission.tenant_resolver');

        if (! $resolver) {
            return null;
        }

        $value = $resolver();

        return is_int($value) ? $value : null;
    }
}
