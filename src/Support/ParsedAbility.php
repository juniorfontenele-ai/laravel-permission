<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

final readonly class ParsedAbility
{
    public function __construct(
        public string $resourceKey,
        public string $action,
        public string $scope, // all|self|attached
    ) {
    }
}
