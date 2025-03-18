<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ListingPosted;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listings = User::find(2)
            ->listingsCreated()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('listings.index', ['listings' => $listings]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('listings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $listing = Listing::create([
            'user_id' => request('user_id'),
            'listing_status_id' => request('listing_status_id'),
            'manufacturer_id' => request('manufacturer_id'),
            'title' => request('title'),
            'detail' => request('detail'),
            'budget_currency_id' => request('budget_currency_id'),
            'budget' => request('budget'),
            'use_default_location' => request('use_default_location'),
            'override_address_line1' => request('override_address_line1'),
            'override_address_line2' => request('override_address_line2'),
            'override_country_id' => request('override_country_id'),
            'override_postcode' => request('override_postcode'),
            'expiry' => request('expiry'),
        ]);

        Mail::to($listing->user)->send(
            new ListingPosted($listing)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        return view('listings.show', ['listing' => $listing]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        return view('listings.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        //
    }

    public function search()
    {
        $query = Listing::where('published_at', '<', now())->orderBy('published_at', 'desc');
        $listingCount = $query->count();
        $listings = $query->limit(30)->get();
        return view('listings.search', ['listings' => $listings, 'listingCount' => $listingCount]);
    }
}
