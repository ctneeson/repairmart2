<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        // Only admins can search/list users
        return $user->hasRole('admin')
            ? Response::allow()
            : Response::denyWithStatus(403, 'You do not have permission to view user listings.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): Response
    {
        // Users can view their own profile or admins can view any profile
        return $user->id === $model->id || $user->hasRole('admin')
            ? Response::allow()
            : Response::denyWithStatus(403, 'You do not have permission to view this profile.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        // Users can update their own profile or admins can update any profile
        return $user->id === $model->id || $user->hasRole('admin')
            ? Response::allow()
            : Response::denyWithStatus(403, 'You do not have permission to update this profile.');
    }

    /**
     * Determine whether the user can assign admin role.
     */
    public function assignAdminRole(User $user): Response
    {
        // Only admins can assign admin role
        return $user->hasRole('admin')
            ? Response::allow()
            : Response::denyWithStatus(403, 'You do not have permission to assign admin privileges.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        // Users can delete their own account
        if ($user->id === $model->id) {
            return Response::allow();
        }

        // Admins can delete other user accounts, but not their own through admin interface
        if ($user->hasRole('admin') && $user->id !== $model->id) {
            return Response::allow();
        }

        // Otherwise, deny permission
        return Response::denyWithStatus(403, 'You do not have permission to delete this account.');
    }
}