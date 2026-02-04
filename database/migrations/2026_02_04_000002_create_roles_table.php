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

            // nullable for single-tenant; once multi-tenancy is enabled, you can enforce not-null.
            $table->unsignedBigInteger('tenant_id')->nullable();

            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();

            $table->unique(['tenant_id', 'name', 'guard_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(PermissionConfig::table('roles'));
    }
};
