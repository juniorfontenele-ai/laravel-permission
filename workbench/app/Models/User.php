<?php

declare(strict_types = 1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JuniorFontenele\LaravelPermission\Traits\InteractsWithPermissions;

class User extends Authenticatable
{
    use HasFactory;
    use InteractsWithPermissions;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
