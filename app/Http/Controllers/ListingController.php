<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingRequest;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ListingPosted;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
    public function create(Request $request)
    {
        return view('listings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreListingRequest $request)
    {
        try {
            // Validate the request data
            $validated = $request->validated();

            // Create the listing
            $listing = Listing::create([
                'user_id' => 2,
                'status_id' => 1,
                'manufacturer_id' => $validated['manufacturer_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'currency_id' => $validated['currency_id'],
                'budget' => $validated['budget'],
                'use_default_location' => $validated['use_default_location'],
                'address_line1' => $validated['address_line1'] ?? null,
                'address_line2' => $validated['address_line2'] ?? null,
                'city' => $validated['city'] ?? null,
                'postcode' => $validated['postcode'] ?? null,
                'country_id' => $validated['country_id'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'expiry_days' => $validated['expiry_days'],
                'published_at' => $validated['published_at'] ?? null,
            ]);

            // Attach products to the listing
            if (!empty($validated['product_ids'])) {
                $listing->products()->attach($validated['product_ids']);
            }

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $i => $file) {
                    $path = $file->store('attachments', 'public');
                    $listing->attachments()
                        ->create([
                            'path' => $path,
                            'position' => $i + 1,
                            'mime_type' => $file->getMimeType()
                        ]);
                }
            }

            return redirect()->route('listings.index')
                ->with('success', 'Listing created successfully.');

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating listing: ' . $e->getMessage());

            // Check if it's a file size issue
            if ($e instanceof \Symfony\Component\HttpFoundation\File\Exception\FileException) {
                return redirect()->back()->withErrors([
                    'attachments' => 'There was an issue with your file uploads. Please check the file sizes and formats.'
                ])->withInput();
            }

            return redirect()->back()->withErrors([
                'general' => 'An error occurred while creating your listing. Please try again.'
            ])->withInput();
        }
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
        return view('listings.edit', ['listing' => $listing]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreListingRequest $request, Listing $listing)
    {
        $validated = $request->validated();
        $validated['use_default_location'] = (bool) $request->use_default_location;
        $products = $validated['product_ids'];

        $listing->update($validated);
        $listing->products()->sync($products);

        return redirect()->route('listings.index')
            ->with('success', 'Listing updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        $listing->delete();
        return redirect()->route('listings.index')
            ->with('success', 'Listing deleted successfully.');
    }

    public function search(Request $request)
    {
        dump($request->all());
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

        if ($manufacturers) {
            $query->whereIn('manufacturer_id', $manufacturers);
        }

        if ($products) {
            $query->whereHas('products', function ($q) use ($products) {
                $q->whereIn('product_id', $products);
            });
        }

        if ($countries) {
            $query->where(function ($q) use ($countries) {
                $q->where(function ($q) use ($countries) {
                    $q->where('use_default_location', 0)
                        ->whereIn('country_id', $countries);
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

    public function listingAttachments(Listing $listing)
    {
        return view('listings.attachments', ['listing' => $listing]);
    }

    public function updateAttachments(Request $request, Listing $listing)
    {
        // Handle deletions
        if ($request->has('delete_attachments')) {
            $attachmentsToDelete = Attachment::whereIn('id', $request->delete_attachments)
                ->where('listing_id', $listing->id)
                ->get();

            foreach ($attachmentsToDelete as $attachment) {
                if (Storage::exists($attachment->path)) {
                    Storage::delete($attachment->path);
                }
                $attachment->delete();
            }
        }

        // Handle position updates
        if ($request->has('positions')) {
            foreach ($request->positions as $id => $position) {
                Attachment::where('id', $id)
                    ->where('listing_id', $listing->id)
                    ->update(['position' => $position]);
            }
        }

        return redirect()->back()->with('success', 'Attachments updated successfully.');
    }

    public function addAttachments(Request $request, Listing $listing)
    {
        // Get attachments from request
        $attachments = $request->file('attachments') ?? [];

        // Get max position from existing attachments
        $position = $listing->attachments()->max('position') ?? 0;
        foreach ($attachments as $attachment) {
            $path = $attachment->store('attachments', 'public');
            $listing->attachments()->create([
                'path' => $path,
                'position' => $position + 1,
                'mime_type' => $attachment->getMimeType()
            ]);
            $position++;
        }
        return redirect()->back()->with('success', 'Attachments added successfully.');
    }
}