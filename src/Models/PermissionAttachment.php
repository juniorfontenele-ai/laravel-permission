<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Models;

use Illuminate\Database\Eloquent\Model;
use JuniorFontenele\LaravelPermission\Models\Concerns\UsesPermissionTables;

class PermissionAttachment extends Model
{
    use UsesPermissionTables;

    protected static string $permissionTableKey = 'permission_attachments';

    protected $fillable = [
        'tenant_id',
        'permission_id',
        'subject_type',
        'subject_id',
        'resource_type',
        'resource_id',
    ];
}
