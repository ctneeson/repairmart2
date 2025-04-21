<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingRequest;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Mail\ListingPosted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;
use App\Models\ListingStatus;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $listings = $request->user()
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
        // Keep all authorization checks
        if (!Gate::allows('create', Listing::class)) {
            $user = auth()->user();

            if (!$user->hasAddress()) {
                return redirect()->route('profile.index')
                    ->with('warning', 'Please add an address to your profile before creating a listing.');
            }

            if (!($user->hasRole('customer') || $user->hasRole('admin'))) {
                return redirect()->route('profile.index')
                    ->with('warning', 'To create a listing, please add the \'customer\' role to your account.');
            }
        }

        // Get relisted data from session if available
        $relistData = session('relist_data') ?? [];

        // Get attachment information for preview
        $attachments = [];
        if (!empty($relistData['attachment_ids'])) {
            $attachments = \App\Models\Attachment::whereIn('id', $relistData['attachment_ids'])->get();
        }

        return view('listings.create', compact('relistData', 'attachments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreListingRequest $request)
    {
        if (!Gate::allows('create', Listing::class)) {
            return redirect()->route('profile.index')
                ->with('warning', 'Please add an address to your profile before creating a listing.');
        }

        try {
            // Validate the request data
            $validated = $request->validated();

            // Create the listing
            $listing = Listing::create([
                'user_id' => Auth::id(),
                'status_id' => 1, // Open
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
                'published_at' => $validated['published_at'] ?? now(),
            ]);

            // Attach products to the listing
            if (!empty($validated['product_ids'])) {
                $listing->products()->attach($validated['product_ids']);
            }

            // Handle new file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $i => $file) {
                    $path = $file->store('attachments', 'public');
                    $listing->attachments()->create([
                        'path' => $path,
                        'position' => $i + 1,
                        'mime_type' => $file->getMimeType(),
                        'user_id' => Auth::id()
                    ]);
                }
            }

            // Handle duplicate attachments from relisted listing
            if ($request->has('duplicate_attachments') && is_array($request->duplicate_attachments)) {
                $position = $listing->attachments()->max('position') ?? 0;

                foreach ($request->duplicate_attachments as $attachmentId) {
                    $originalAttachment = Attachment::find($attachmentId);

                    if ($originalAttachment) {
                        // Create a new file copy to prevent deletion issues if original listing is deleted
                        $originalPath = 'public/' . $originalAttachment->path;
                        $newPath = 'attachments/' . basename($originalAttachment->path);

                        if (Storage::exists($originalPath)) {
                            // Copy the file to a new path
                            $contents = Storage::get($originalPath);
                            Storage::put('public/' . $newPath, $contents);

                            // Create new attachment record
                            $position++;
                            $listing->attachments()->create([
                                'path' => $newPath,
                                'position' => $position,
                                'mime_type' => $originalAttachment->mime_type,
                                'user_id' => Auth::id(),
                                'title' => $originalAttachment->title,
                                'description' => $originalAttachment->description
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('listings.index')
                ->with('success', 'Listing created successfully.');

        } catch (\Exception $e) {
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
        Gate::authorize('update', $listing);

        return view('listings.edit', ['listing' => $listing]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreListingRequest $request, Listing $listing)
    {
        Gate::authorize('update', $listing);

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
        Gate::authorize('delete', $listing);

        $listing->delete();
        return redirect()->route('listings.index')
            ->with('success', 'Listing deleted successfully.');
    }

    /**
     * Search for listings based on filters.
     */
    public function search(Request $request)
    {
        $products = $request->product_ids;
        $countries = $request->country_ids;
        $manufacturers = $request->manufacturer_ids;
        $sort = $request->input('sort', '-published_at');
        $page = $request->input('page', 1);

        // Create a cache key based on the search parameters
        $cacheKey = 'listing_search_' . md5(json_encode([
            'products' => $products,
            'countries' => $countries,
            'manufacturers' => $manufacturers,
            'sort' => $sort,
            'page' => $page
        ]));

        // Cache only the query results, not the view
        $listings = cache()->remember($cacheKey, 300, function () use ($products, $countries, $manufacturers, $sort) {
            // Start with active listings (published, open, not expired)
            $query = Listing::active()
                ->with([
                    'country',
                    'manufacturer',
                    'currency',
                    'primaryAttachment',
                    'products',
                    'watchlistUsers'
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
                $query->whereIn('country_id', $countries);
            }

            if (str_starts_with($sort, '-')) {
                $sort = substr($sort, 1);
                $query->orderBy($sort, 'desc');
            } else {
                $query->orderBy($sort, 'asc');
            }

            return $query->paginate(15)->withQueryString();
        });

        // Generate and return the view with the cached results
        return view('listings.search', ['listings' => $listings]);
    }

    /**
     * Show the attachments for a listing.
     */
    public function listingAttachments(Listing $listing)
    {
        Gate::authorize('update', $listing);
        return view('listings.attachments', ['listing' => $listing]);
    }

    /**
     * Update the attachments for a listing.
     */
    public function updateAttachments(Request $request, Listing $listing)
    {
        Gate::authorize('update', $listing);

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

    /**
     * Add attachments to a listing.
     */
    public function addAttachments(Request $request, Listing $listing)
    {
        Gate::authorize('update', $listing);

        // Get attachments from request
        $attachments = $request->file('attachments') ?? [];

        // Get max position from existing attachments
        $position = $listing->attachments()->max('position') ?? 0;
        foreach ($attachments as $attachment) {
            $path = $attachment->store('attachments', 'public');
            $listing->attachments()->create([
                'path' => $path,
                'position' => $position + 1,
                'mime_type' => $attachment->getMimeType(),
                'user_id' => Auth::id() // Add the current user's ID
            ]);
            $position++;
        }
        return redirect()->back()->with('success', 'Attachments added successfully.');
    }

    /**
     * Show the phone number for a listing.
     */
    public function showPhone(Listing $listing)
    {
        \Log::info('Phone number requested for listing: ' . $listing->id);
        return response()->json([
            'phone' => $listing->phone,
            'success' => true
        ]);
    }

    /**
     * Prepare a new listing based on an expired one.
     *
     * @param Listing $listing
     * @return \Illuminate\Http\RedirectResponse
     */
    public function relist(Listing $listing)
    {
        // Check if the listing is expired and belongs to the current user
        if ($listing->status->name !== 'Closed-Expired' || $listing->user_id !== auth()->id()) {
            return redirect()->route('listings.index')
                ->with('error', 'You can only relist your own expired listings.');
        }

        // Store attachment data so we can duplicate them later
        $attachmentIds = $listing->attachments->pluck('id')->toArray();

        // Store the old listing data in the session
        session()->flash('relist_data', [
            'title' => $listing->title,
            'description' => $listing->description,
            'manufacturer_id' => $listing->manufacturer_id,
            'country_id' => $listing->country_id,
            'city' => $listing->city,
            'postcode' => $listing->postcode,
            'address_line1' => $listing->address_line1,
            'address_line2' => $listing->address_line2,
            'currency_id' => $listing->currency_id,
            'budget' => $listing->budget,
            'phone' => $listing->phone,
            'expiry_days' => $listing->expiry_days,
            'use_default_location' => $listing->use_default_location,
            'product_ids' => $listing->products->pluck('id')->toArray(),
            'attachment_ids' => $attachmentIds, // Store attachment IDs to duplicate later
        ]);

        return redirect()->route('listings.create')
            ->with('success', 'Create a new listing using the details from your expired listing.');
    }
}