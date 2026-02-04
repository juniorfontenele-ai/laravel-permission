<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JuniorFontenele\LaravelPermission\Support\PermissionConfig;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(PermissionConfig::table('roles'), function (Blueprint $table) {
            $table->bigIncrements('id');

            if (PermissionConfig::tenancyEnabled()) {
                // nullable for single-tenant; once multi-tenancy is enabled, you can enforce not-null.
                $table->unsignedBigInteger(PermissionConfig::tenantColumn())->nullable();
            }

            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();

            if (PermissionConfig::tenancyEnabled()) {
                $table->unique([PermissionConfig::tenantColumn(), 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(PermissionConfig::table('roles'));
    }
};
