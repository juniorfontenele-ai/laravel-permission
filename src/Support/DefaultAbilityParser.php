<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use JuniorFontenele\LaravelPermission\Contracts\AbilityParser;

final class DefaultAbilityParser implements AbilityParser
{
    public function parse(string $ability): ?ParsedAbility
    {
        // expected: {resource}.{action}.{scope}
        $parts = explode('.', $ability);

        if (count($parts) !== 3) {
            return null;
        }

        [$resourceKey, $action, $scope] = $parts;

        if ($resourceKey === '' || $action === '' || $scope === '') {
            return null;
        }

        if (! in_array($scope, ['all', 'self', 'attached'], true)) {
            return null;
        }

        return new ParsedAbility($resourceKey, $action, $scope);
    }
}
