<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Listing;

class WatchlistController extends Controller
{
    /**
     * Display a listing of the watchlist.
     */
    public function index()
    {
        $listings = Auth::user()
            ->watchlistListings()
            ->with([
                'country',
                'customer',
                'customer.country',
                'manufacturer',
                'currency',
                'primaryAttachment',
                'products'
            ])
            ->paginate(15);

        return view('watchlist.index', ['listings' => $listings]);
    }

    /**
     * Add or remove a listing from the watchlist.
     */
    public function storeDestroy(Listing $listing)
    {
        $user = Auth::user();

        $listingExists = $user->watchlistListings()
            ->where('listing_id', $listing->id)->exists();
        // Check if the listing is already in the watchlist
        if ($listingExists) {
            // Remove from watchlist
            $user->watchlistListings()->detach($listing);
            return back()->with('success', 'Listing removed from watchlist.');
        } else {
            // Add to watchlist
            $user->watchlistListings()->attach($listing);
            return back()->with('success', 'Listing added to watchlist.');
        }
    }
}
