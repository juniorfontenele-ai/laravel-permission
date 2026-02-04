<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use JuniorFontenele\LaravelPermission\Contracts\AbilityParser as AbilityParserContract;
use JuniorFontenele\LaravelPermission\Contracts\AuthorizationService as AuthorizationServiceContract;
use JuniorFontenele\LaravelPermission\Support\DefaultAbilityParser;
use JuniorFontenele\LaravelPermission\Support\PermissionManager;
use JuniorFontenele\LaravelPermission\Support\PermissionRegistrar;
use JuniorFontenele\LaravelPermission\Support\PermissionStore;
use JuniorFontenele\LaravelPermission\Support\RoleStore;

final class LaravelPermissionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/permission.php', 'permission');

        $this->app->singleton(PermissionStore::class);
        $this->app->singleton(RoleStore::class);

        $this->app->singleton(PermissionRegistrar::class, function ($app) {
            return new PermissionRegistrar(
                permissions: $app->make(PermissionStore::class),
                roles: $app->make(RoleStore::class),
            );
        });

        $this->app->singleton(PermissionManager::class, function ($app) {
            return new PermissionManager(
                permissions: $app->make(PermissionStore::class),
                roles: $app->make(RoleStore::class),
            );
        });

        $this->app->singleton(Permission::class, fn ($app) => $app->make(PermissionManager::class));

        $this->app->bind(AbilityParserContract::class, DefaultAbilityParser::class);

        $this->app->bind(AuthorizationServiceContract::class, function ($app) {
            return $app->make(PermissionManager::class)->authorizationService();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/permission.php' => config_path('permission.php'),
        ], 'permission-config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Gate::before(function (Authenticatable $user, string $ability, array $arguments = []) {
            /** @var AuthorizationServiceContract $authz */
            $authz = app(AuthorizationServiceContract::class);

            return $authz->check($user, $ability, $arguments);
        });
    }
}
