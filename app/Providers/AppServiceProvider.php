<?php

namespace App\Providers;

use Illuminate\Auth\Access\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;
use App\Models\Listing;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('pagination');
        View::share("year", date('Y'));

        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('admin')) {
                return Response::allow();
            }

            // if ($user->isGuest()) {
            //     return Response::deny('You must be logged in to perform this action.');
            // }
        });

        Gate::define('update-listing', function (User $user, Listing $listing) {
            return $user->id === $listing->user_id || $user->hasRole('admin')
                ? Response::allow()
                : Response::denyWithStatus(404);
        });

        Gate::define('delete-listing', function (User $user, Listing $listing) {
            return $user->id === $listing->user_id || $user->hasRole('admin')
                ? Response::allow()
                : Response::denyWithStatus(404);
        });
    }
}
