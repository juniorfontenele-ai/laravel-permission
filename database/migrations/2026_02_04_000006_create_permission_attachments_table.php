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
        Schema::create(PermissionConfig::table('permission_attachments'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('tenant_id')->nullable();

            $table->unsignedBigInteger('permission_id');

            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');

            $table->string('resource_type');
            $table->unsignedBigInteger('resource_id');

            $table->timestamps();

            $table->index(['subject_id', 'subject_type']);
            $table->index(['resource_id', 'resource_type']);
            $table->index(['tenant_id']);

            $table->unique([
                'tenant_id',
                'permission_id',
                'subject_type',
                'subject_id',
                'resource_type',
                'resource_id',
            ], 'permission_attachments_unique');

            $table->foreign('permission_id')
                ->references('id')
                ->on(PermissionConfig::table('permissions'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(PermissionConfig::table('permission_attachments'));
    }
};
