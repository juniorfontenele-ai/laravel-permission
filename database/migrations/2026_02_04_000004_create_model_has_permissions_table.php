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
        Schema::create(PermissionConfig::table('model_has_permissions'), function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');

            if (PermissionConfig::tenancyEnabled()) {
                $table->unsignedBigInteger(PermissionConfig::tenantColumn())->nullable();
            }

            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type']);

            if (PermissionConfig::tenancyEnabled()) {
                $table->index([PermissionConfig::tenantColumn()]);

                $table->primary(
                    ['permission_id', PermissionConfig::tenantColumn(), 'model_id', 'model_type'],
                    'model_has_permissions_primary'
                );
            } else {
                $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_permissions_primary');
            }

            $table->foreign('permission_id')
                ->references('id')
                ->on(PermissionConfig::table('permissions'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(PermissionConfig::table('model_has_permissions'));
    }
};
