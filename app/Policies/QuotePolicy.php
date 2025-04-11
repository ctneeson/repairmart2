<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;
use App\Models\Listing;
use Illuminate\Auth\Access\Response;

class QuotePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quote $quote): Response
    {
        // User can view if they are:
        // 1. The listing owner
        // 2. The quote creator
        // 3. An admin
        $isListingOwner = $user->id === $quote->listing->user_id;
        $isQuoteCreator = $user->id === $quote->user_id;
        $isAdmin = $user->hasRole('admin');

        if ($isListingOwner || $isQuoteCreator || $isAdmin) {
            return Response::allow();
        }

        return Response::denyWithStatus(403, 'You do not have permission to view this quote.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?Listing $listing = null): Response
    {
        // Check if user has address and correct role
        $hasRequirements = !!$user->hasAddress() &&
            ($user->hasRole('specialist') || $user->hasRole('admin'));

        // Check if user is trying to quote their own listing
        $isOwnListing = $listing && $listing->user_id === $user->id;

        if (!$hasRequirements) {
            return Response::deny('You must have an address and be a specialist or admin to create quotes.');
        }

        if ($isOwnListing) {
            return Response::deny('You cannot create a quote for your own listing.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quote $quote): Response
    {
        // First check if user is owner or admin
        if (!($user->id === $quote->user_id || $user->hasRole('admin'))) {
            return Response::denyWithStatus(404);
        }

        // Then check if quote is open (admins can edit regardless of status)
        if ($quote->status->name !== 'Open' && !$user->hasRole('admin')) {
            return Response::deny('This quote cannot be updated because its status is not "Open".');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quote $quote): Response
    {
        // First check if user is owner or admin
        if (!($user->id === $quote->user_id || $user->hasRole('admin'))) {
            return Response::denyWithStatus(404);
        }

        // Then check if quote is open (admins can delete regardless of status)
        if ($quote->status->name !== 'Open' && !$user->hasRole('admin')) {
            return Response::deny('This quote cannot be deleted because its status is not "Open".');
        }

        return Response::allow();
    }
}
