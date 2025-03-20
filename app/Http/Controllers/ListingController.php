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
            ->with(['primaryAttachment', 'manufacturer', 'products'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

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
        dd($request->all());

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

    public function search(Request $request)
    {
        // dump($request->product_ids);
        // dump($request->manufacturer_ids);
        // dump($request->country_ids);
        $products = $request->product_ids;
        $countries = $request->country_ids;
        $manufacturers = $request->manufacturer_ids;
        $sort = $request->input('sort', '-published_at');

        $query = Listing::where('published_at', '<', now())
            ->with([
                'country',
                'customer',
                'customer.country',
                'manufacturer',
                'currency',
                'primaryAttachment',
                'products'
            ]);

        if ($products) {
            $query->whereHas('products', function ($q) use ($products) {
                $q->whereIn('product_id', $products);
            });
        }

        if ($countries) {
            $query->where(function ($q) use ($countries) {
                $q->where(function ($q) use ($countries) {
                    $q->where('use_default_location', 0)
                        ->whereIn('override_country_id', $countries);
                })->orWhereHas('customer', function ($q) use ($countries) {
                    $q->where('use_default_location', 1)
                        ->whereIn('country_id', $countries);
                });
            });
        }

        if (str_starts_with($sort, '-')) {
            $sort = substr($sort, 1);
            $query->orderBy($sort, 'desc');
        } else {
            $query->orderBy($sort, 'asc');
        }

        $listings = $query->paginate(15)
            ->withQueryString();

        // $listingCount = $query->count();
        // $listings = $query->limit(30)->get();

        return view('listings.search', ['listings' => $listings]);
    }

    public function watchlist()
    {
        $listings = User::find(2)
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

        return view('listings.watchlist', ['listings' => $listings]);
    }
}
