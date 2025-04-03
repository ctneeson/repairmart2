<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class ListingPolicy
{
    // public function before(?User $user, string $ability, $arg)
    // {
    //     dump($ability, $arg);
    // }

    // public function after(?User $user, string $ability, $arg)
    // {
    //     dump($ability, $arg);
    // }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return !!$user->hasAddress();
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
