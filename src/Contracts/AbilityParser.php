<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Contracts;

use JuniorFontenele\LaravelPermission\Support\ParsedAbility;

interface AbilityParser
{
    public function parse(string $ability): ?ParsedAbility;
}
