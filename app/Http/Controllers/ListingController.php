<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\User;
use Hamcrest\Type\IsBoolean;
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
        // Check if attachments are present before validation
        $attachments = $request->file('attachments') ?: [];
        dd($request->all(), $attachments);

        // Validate the request data
        $validated = $request->validate([
            'manufacturer_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'currency_id' => 'required|integer',
            'budget' => 'required|numeric',
            'use_default_address' => 'required|boolean',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:255',
            'country_id' => 'nullable|integer',
            'expiry_days' => 'required|integer',
            'published_at' => 'nullable|date',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|distinct',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt|max:20000',
        ]);

        // Check if attachments are present after validation
        $attachments = $request->file('attachments') ?: [];
        dd($validated, $attachments);

        // Create the listing
        $listing = Listing::create([
            'user_id' => 2,
            'status_id' => 1,
            'manufacturer_id' => $validated['manufacturer_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'budget_currency_id' => $validated['currency_id'],
            'budget' => $validated['budget'],
            'use_default_location' => $validated['use_default_address'],
            'override_address_line1' => $validated['address_line1'] ?? null,
            'override_address_line2' => $validated['address_line2'] ?? null,
            'override_city' => $validated['city'] ?? null,
            'override_postcode' => $validated['postcode'] ?? null,
            'override_country_id' => $validated['country_id'] ?? null,
            'expiry_days' => $validated['expiry_days'],
            'published_at' => $validated['published_at'] ?? null,
        ]);

        // Attach products to the listing
        if (!empty($validated['product_ids'])) {
            $listing->products()->attach($validated['product_ids']);
        }

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                // Save the file path to the database or perform other actions as needed
                // Example: $listing->attachments()->create(['path' => $path]);
            }
        }

        return redirect()->route('listings.index')
            ->with('success', 'Listing created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        if (!$listing->published_at) {
            abort(404);
        }
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
