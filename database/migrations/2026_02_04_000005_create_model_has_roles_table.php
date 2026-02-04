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
        Schema::create(PermissionConfig::table('model_has_roles'), function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');

            if (PermissionConfig::tenancyEnabled()) {
                $table->unsignedBigInteger(PermissionConfig::tenantColumn())->nullable();
            }

            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type']);

            if (PermissionConfig::tenancyEnabled()) {
                $table->index([PermissionConfig::tenantColumn()]);

                $table->primary(
                    ['role_id', PermissionConfig::tenantColumn(), 'model_id', 'model_type'],
                    'model_has_roles_primary'
                );
            } else {
                $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_primary');
            }

            $table->foreign('role_id')
                ->references('id')
                ->on(PermissionConfig::table('roles'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(PermissionConfig::table('model_has_roles'));
    }
};
