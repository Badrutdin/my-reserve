<?php

namespace App\Security;

final class PermissionHierarchy
{
    private const HIERARCHY = [
        Permission::EDIT_CLIENTS->value => [
            Permission::VIEW_CLIENTS->value,
        ],
        Permission::DELETE_CLIENTS->value => [
            Permission::EDIT_CLIENTS->value,
            Permission::VIEW_CLIENTS->value,
        ],
    ];

    public static function getDependencies(string $permission): array
    {
        return self::HIERARCHY[$permission] ?? [];
    }
}
