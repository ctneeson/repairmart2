<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class ListingPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return !!$user->hasAddress() && ($user->hasRole('customer') || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Listing $listing): Response
    {
        return $user->id === $listing->user_id || $user->hasRole('admin')
            ? Response::allow()
            : Response::denyWithStatus(404);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Listing $listing): Response
    {
        return $user->id === $listing->user_id || $user->hasRole('admin')
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
