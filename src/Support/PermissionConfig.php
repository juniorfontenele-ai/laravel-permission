<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

final class PermissionConfig
{
    public static function modelClass(string $key): string
    {
        /** @var string $class */
        $class = config("permission.models.$key");

        return $class;
    }

    public static function table(string $key): string
    {
        /** @var string $table */
        $table = config("permission.tables.$key", $key);

        return $table;
    }
}
