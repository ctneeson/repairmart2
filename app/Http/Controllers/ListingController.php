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

        return view('listings.create');
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
                'published_at' => $validated['published_at']
                    ? \Carbon\Carbon::parse($validated['published_at'])
                        ->startOfDay()->setTimezone('UTC') : now(),
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
                            'mime_type' => $file->getMimeType(),
                            'user_id' => Auth::id()
                        ]);
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
     *
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function edit(Listing $listing)
    {
        // Verify the current user owns this listing
        if ($listing->user_id !== auth()->id()) {
            return redirect()->route('listings.index')
                ->with('error', 'You can only edit your own listings.');
        }

        // Check if we're relisting
        $isRelisting = session('is_relisting', false);

        // If relisting, change some defaults for the form
        if ($isRelisting) {
            // These values will be used in the form but not saved until submission
            $listing->published_at = now()->format('Y-m-d');
        }

        return view('listings.edit', compact('listing', 'isRelisting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreListingRequest $request, Listing $listing)
    {
        Gate::authorize('update', $listing);

        $validated = $request->validated();
        $validated['use_default_location'] = (bool) $request->use_default_location;
        $products = $validated['product_ids'] ?? [];

        // Check if we're relisting an expired listing
        $isRelisting = session('is_relisting', false);

        if ($isRelisting) {
            // Reset the expiration and status for relisted listings
            $validated['status_id'] = 1; // Open
            $validated['expired_at'] = null;
            $validated['published_at'] = $validated['published_at']
                ? \Carbon\Carbon::parse($validated['published_at'])
                    ->startOfDay()->setTimezone('UTC') : now();

            // Clear the relisting flag from session
            session()->forget('is_relisting');
        }

        $listing->update($validated);
        $listing->products()->sync($products);

        $successMessage = $isRelisting
            ? 'Listing relisted successfully.'
            : 'Listing updated successfully.';

        return redirect()->route('listings.index')
            ->with('success', $successMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        Gate::authorize('delete', $listing);

        $retractedStatus = ListingStatus::where('name', 'Closed-Retracted')->first();

        if (!$retractedStatus) {
            \Log::warning('Closed-Retracted status not found when deleting listing #' . $listing->id);
            $retractedStatus = ListingStatus::create(['name' => 'Closed-Retracted']);
        }

        $listing->update([
            'status_id' => $retractedStatus->id,
        ]);

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
        $userId = $request->user_id;
        $sort = $request->input('sort', '-published_at');
        $page = $request->input('page', 1);

        // Create a cache key based on the search parameters
        $cacheKey = 'listing_search_' . md5(json_encode([
            'products' => $products,
            'countries' => $countries,
            'manufacturers' => $manufacturers,
            'user_id' => $userId, // Add this to the cache key
            'sort' => $sort,
            'page' => $page
        ]));

        // Cache only the query results, not the view
        $listings = cache()->remember($cacheKey, 300, function () use ($products, $countries, $manufacturers, $userId, $sort) {
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

            // Add the user_id filter if provided
            if ($userId) {
                $query->where('user_id', $userId);
            }

            if ($sort === 'expiry_asc') {
                $query->orderByExpiryDate('asc');
            } else if ($sort === 'expiry_desc') {
                $query->orderByExpiryDate('desc');
            } else if (str_starts_with($sort, '-')) {
                $sort = substr($sort, 1);
                $query->orderBy($sort, 'desc');
            } else {
                $query->orderBy($sort, 'asc');
            }

            return $query->paginate(15)->withQueryString();
        });

        // Add this for debugging time zone issues:
        // dd([
        //     'server_time' => now()->format('Y-m-d H:i:s e'),
        //     'utc_time' => now()->setTimezone('UTC')->format('Y-m-d H:i:s e')
        // ]);

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
     * Reopen an expired listing
     *
     * @param Listing $listing
     * @return \Illuminate\Http\RedirectResponse
     */
    public function relist(Listing $listing)
    {
        Gate::authorize('restore', $listing);

        // Check if the listing is expired and belongs to the current user
        if ($listing->status->name !== 'Closed-Expired' || $listing->user_id !== auth()->id()) {
            return redirect()->route('listings.index')
                ->with('error', 'You can only relist your own expired listings.');
        }

        // Set session flag to indicate we're relisting (for the edit form)
        session()->flash('is_relisting', true);

        // Redirect to the edit form
        return redirect()->route('listings.edit', $listing)
            ->with('success', 'Update the listing details and submit to relist it.');
    }
}