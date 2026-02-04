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
        Schema::create(PermissionConfig::table('role_has_permissions'), function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->primary(['permission_id', 'role_id']);

            $table->foreign('permission_id')
                ->references('id')
                ->on(PermissionConfig::table('permissions'))
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on(PermissionConfig::table('roles'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(PermissionConfig::table('role_has_permissions'));
    }
};
