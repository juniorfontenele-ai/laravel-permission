<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelPermission\Support;

use Illuminate\Contracts\Auth\Authenticatable;

final class PermissionAttachment
{
    public static function attach(Authenticatable $user, int $permissionId, object $resource, ?int $tenantId = null): void
    {
        $attachmentClass = PermissionConfig::modelClass('attachment');

        $attributes = [
            'permission_id' => $permissionId,
            'subject_type' => $user::class,
            'subject_id' => (int) $user->getAuthIdentifier(),
            'resource_type' => $resource::class,
            'resource_id' => (int) $resource->getKey(),
        ];

        if (PermissionConfig::tenancyEnabled()) {
            $attributes[PermissionConfig::tenantColumn()] = $tenantId;
        }

        $attachmentClass::query()->firstOrCreate($attributes);
    }

    public static function exists(Authenticatable $user, int $permissionId, object $resource, ?int $tenantId = null): bool
    {
        $attachmentClass = PermissionConfig::modelClass('attachment');

        $query = $attachmentClass::query()
            ->where('permission_id', $permissionId)
            ->where('subject_type', $user::class)
            ->where('subject_id', (int) $user->getAuthIdentifier())
            ->where('resource_type', $resource::class)
            ->where('resource_id', (int) $resource->getKey());

        if (PermissionConfig::tenancyEnabled()) {
            $query->where(PermissionConfig::tenantColumn(), $tenantId);
        }

        return $query->exists();
    }
}
