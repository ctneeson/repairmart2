<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): Response
    {
        // User can view if they are:
        // 1. The customer (listing owner)
        // 2. The specialist (quote creator)
        // 3. An admin
        $isCustomer = $user->id === $order->customer_id;
        $isSpecialist = $user->id === $order->specialist_id;
        $isAdmin = $user->hasRole('admin');

        if ($isCustomer || $isSpecialist || $isAdmin) {
            return Response::allow();
        }

        return Response::denyWithStatus(403, 'You do not have permission to view this order.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Quote $quote): Response
    {
        // Only the quote's customer (listing owner) or an admin can create an order
        $isCustomer = $user->id === $quote->listing->user_id;
        $isAdmin = $user->hasRole('admin');

        if ($isCustomer || $isAdmin) {
            return Response::allow();
        }

        return Response::denyWithStatus(403, 'Only the customer or an admin can create an order from this quote.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): Response
    {
        // Only the customer (listing owner), the specialist (quote creator), or an admin can update an order
        if (
            !($user->id === $order->customer_id
                || $user->id === $order->quote->user_id
                || $user->hasRole('admin'))
        ) {
            return Response::denyWithStatus(404);
        }

        // Check if the order is in a status that allows updates
        if (
            ($order->status->name === 'Closed'
                || $order->status->name === 'Cancelled') && !$user->hasRole('admin')
        ) {
            return Response::deny('This order can no longer be updated because its status is \'' .
                $order->status->name . '\'.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): Response
    {
        // Only admins can delete orders
        if ($user->hasRole('admin')) {
            return Response::allow();
        }

        return Response::denyWithStatus(403, 'You do not have permission to delete this order.');
    }

    /**
     * Determine whether the user can update the order status.
     */
    public function updateStatus(User $user, Order $order): Response
    {
        $isCustomer = $user->id === $order->customer_id;
        $isSpecialist = $user->id === $order->specialist_id;
        $isAdmin = $user->hasRole('admin');

        if ($isCustomer || $isSpecialist || $isAdmin) {
            return Response::allow();
        }

        return Response::denyWithStatus(403, 'You do not have permission to update this order status.');
    }

    /**
     * Determine whether the user can update the order amount.
     *
     * @param User $user
     * @param Order $order
     * @return \Illuminate\Auth\Access\Response
     */
    public function updateAmount(User $user, Order $order): Response
    {
        // Only the specialist can update the amount, and only when in a status that allows it
        if ($user->id === $order->specialist_id && $order->isAmountEditable()) {
            return Response::allow();
        }

        return Response::denyWithStatus(403, 'Only the repair specialist can update the order amount, and only during price adjustment.');
    }
}
