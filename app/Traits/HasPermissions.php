<?php

namespace App\Traits;

trait HasPermissions
{
    /**
     * Check if the authenticated user has a specific permission.
     */
    protected function authorize(string $permission, string $message = 'Unauthorized action.'): void
    {
        if (!auth()->user()->hasPermission($permission)) {
            abort(403, $message);
        }
    }

    /**
     * Check if the authenticated user has any of the given permissions.
     */
    protected function authorizeAny(array $permissions, string $message = 'Unauthorized action.'): void
    {
        if (!auth()->user()->hasAnyPermission($permissions)) {
            abort(403, $message);
        }
    }

    /**
     * Check if the authenticated user has all of the given permissions.
     */
    protected function authorizeAll(array $permissions, string $message = 'Unauthorized action.'): void
    {
        if (!auth()->user()->hasAllPermissions($permissions)) {
            abort(403, $message);
        }
    }

    /**
     * Check if the authenticated user can perform an action.
     */
    protected function can(string $permission): bool
    {
        return auth()->user()->hasPermission($permission);
    }

    /**
     * Check if the authenticated user cannot perform an action.
     */
    protected function cannot(string $permission): bool
    {
        return !$this->can($permission);
    }
}
