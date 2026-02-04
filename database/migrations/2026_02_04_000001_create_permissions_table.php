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
        Schema::create(PermissionConfig::table('permissions'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();

            // Permission names are unique globally (guard_name is informational only).
            $table->unique(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(PermissionConfig::table('permissions'));
    }
};
