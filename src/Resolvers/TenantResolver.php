<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Resolvers;

interface TenantResolver
{
    public function resolveTenantId(): ?int;
}
