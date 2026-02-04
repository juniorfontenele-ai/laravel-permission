<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Contracts\Auth\Authenticatable;

final class PermissionAttachment
{
    public static function attach(Authenticatable $user, int $permissionId, object $resource, ?int $tenantId = null): void
    {
        $attachmentClass = PermissionConfig::modelClass('attachment');

        $attachmentClass::query()->firstOrCreate([
            'tenant_id' => $tenantId,
            'permission_id' => $permissionId,
            'subject_type' => $user::class,
            'subject_id' => (int) $user->getAuthIdentifier(),
            'resource_type' => $resource::class,
            'resource_id' => (int) $resource->getKey(),
        ]);
    }

    public static function exists(Authenticatable $user, int $permissionId, object $resource, ?int $tenantId = null): bool
    {
        $attachmentClass = PermissionConfig::modelClass('attachment');

        return $attachmentClass::query()
            ->where('tenant_id', $tenantId)
            ->where('permission_id', $permissionId)
            ->where('subject_type', $user::class)
            ->where('subject_id', (int) $user->getAuthIdentifier())
            ->where('resource_type', $resource::class)
            ->where('resource_id', (int) $resource->getKey())
            ->exists();
    }
}
