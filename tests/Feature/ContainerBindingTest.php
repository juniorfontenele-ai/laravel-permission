<?php

declare(strict_types = 1);

use JuniorFontenele\LaravelPermission\Facades\Permission as PermissionFacade;
use JuniorFontenele\LaravelPermission\Permission;
use JuniorFontenele\LaravelPermission\Support\PermissionManager;

it('resolves Permission entry point consistently (container + facade)', function () {
    $entry = app(Permission::class);
    $manager = app(PermissionManager::class);

    expect($entry)->toBeInstanceOf(Permission::class);
    expect($manager)->toBeInstanceOf(PermissionManager::class);

    // PermissionManager is an alias of the public entry point.
    expect($manager)->toBe($entry);

    /** @var object $facadeRoot */
    $facadeRoot = PermissionFacade::getFacadeRoot();
    expect($facadeRoot)->toBe($entry);
});
