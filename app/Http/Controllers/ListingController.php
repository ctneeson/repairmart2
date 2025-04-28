<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingRequest;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
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
     * Setup SQLite FTS for listings.
     */
    private function setupSqliteFTS()
    {
        // Only run this once per request
        static $ftsSetup = false;
        if ($ftsSetup)
            return;

        try {
            // First check if FTS5 is available
            \DB::select('SELECT fts5(?)', ['test']);

            // Try to create the FTS table if it doesn't exist
            \DB::statement('CREATE VIRTUAL TABLE IF NOT EXISTS listings_fts USING fts5(title, description)');

            // Check if we need to populate it
            $ftsCount = cache()->remember('listings_fts_count', 300, function () {
                try {
                    return \DB::scalar('SELECT COUNT(*) FROM listings_fts');
                } catch (\Exception $e) {
                    \Log::error("Error counting FTS entries: " . $e->getMessage());
                    return 0;
                }
            });
            $listingsCount = Listing::published()
                ->whereNull('deleted_at')
                ->count();

            if ($ftsCount != $listingsCount) {
                \DB::statement('DELETE FROM listings_fts');

                $listings = Listing::active()
                    ->select('id', 'title', 'description')
                    ->get();

                foreach ($listings as $listing) {
                    $title = str_replace("'", "''", $listing->title);
                    $description = str_replace("'", "''", $listing->description);

                    \DB::statement(
                        "INSERT INTO listings_fts(rowid, title, description) VALUES (?, ?, ?)",
                        [$listing->id, $title, $description]
                    );
                }

                \Log::info("Rebuilt FTS index with {$listingsCount} listings");
            }

            $ftsSetup = true;
        } catch (\Exception $e) {
            \Log::error("Error setting up SQLite FTS: " . $e->getMessage());
        }
    }

    /**
     * Summary of debugFTS
     * @param \Illuminate\Http\Request $request
     * @param mixed $searchText
     * @return never
     */
    private function debugFTS(Request $request, $searchText)
    {
        $this->setupSqliteFTS();

        $result = \DB::select('SELECT rowid, title, description FROM listings_fts');

        $entries = [];
        foreach ($result as $row) {
            $entries[] = [
                'rowid' => $row->rowid,
                'title' => $row->title,
                'description_preview' => substr($row->description, 0, 100) . '...',
            ];
        }

        // Test the actual search query
        $words = explode(' ', trim($searchText));
        $searchTerms = implode(' OR ', array_map(function ($word) {
            return $word . '*';
        }, $words));

        // Try the actual query
        $matchingIds = [];
        $ftsWorking = true;

        try {
            $matches = \DB::select("SELECT rowid FROM listings_fts WHERE listings_fts MATCH ?", [$searchTerms]);
            $matchingIds = array_map(function ($item) {
                return $item->rowid;
            }, $matches);
        } catch (\Exception $e) {
            $matchingIds = ['Error: ' . $e->getMessage()];
            $ftsWorking = false;
        }

        // Compare with LIKE search
        $likeMatches = [];
        try {
            $term = '%' . $searchText . '%';
            $likeResults = \DB::table('listings')
                ->select('id', 'title', 'description')
                ->where('title', 'LIKE', $term)
                ->orWhere('description', 'LIKE', $term)
                ->get();

            $likeMatches = $likeResults->pluck('id')->toArray();
        } catch (\Exception $e) {
            $likeMatches = ['Error: ' . $e->getMessage()];
        }

        dd([
            'fts_entries_count' => count($entries),
            'fts_sample' => array_slice($entries, 0, 5),
            'search_text' => $searchText,
            'search_terms' => $searchTerms,
            'matching_ids_fts' => $matchingIds,
            'matching_ids_like' => $likeMatches,
            'fts_working' => $ftsWorking,
            'differences' => [
                'in_like_not_in_fts' => array_diff($likeMatches, $matchingIds),
                'in_fts_not_in_like' => array_diff($matchingIds, $likeMatches),
            ]
        ]);
    }

    /**
     * Search for listings based on filters.
     */
    public function search(Request $request)
    {
        // Parse request parameters
        $products = $request->product_ids;
        $countries = $request->country_ids;
        $manufacturers = $request->manufacturer_ids;
        $userId = $request->user_id;
        $searchText = $request->input('search_text');
        $sort = $request->input('sort', '-published_at');
        $page = $request->input('page', 1);

        // Debug mode
        if ($request->has('debug') && $searchText) {
            return $this->debugFTS($request, $searchText);
        }

        // Build cache key
        $cacheKey = 'listing_search_' . md5(json_encode([
            'products' => $products,
            'countries' => $countries,
            'manufacturers' => $manufacturers,
            'user_id' => $userId,
            'search_text' => $searchText,
            'sort' => $sort,
            'page' => $page
        ]));

        // Cache the search results
        $listings = cache()->remember(
            $cacheKey,
            300,
            function () use ($products, $countries, $manufacturers, $userId, $searchText, $sort) {
                // Start with active listings
                $query = Listing::active();

                // Apply text search if provided
                if ($searchText) {
                    $this->applyTextSearch($query, $searchText);
                }

                // Apply filters
                $this->applyStandardFilters($query, $products, $countries, $manufacturers, $userId);

                // Apply sorting
                $this->applySorting($query, $sort);

                // Load relationships
                $query->with([
                    'country',
                    'manufacturer',
                    'currency',
                    'primaryAttachment',
                    'products',
                    'watchlistUsers'
                ]);

                return $query->paginate(15)->withQueryString();
            }
        );

        return view('listings.search', ['listings' => $listings]);
    }

    /**
     * Apply text search logic based on database driver
     */
    private function applyTextSearch($query, $searchText)
    {
        $driver = \DB::connection()->getDriverName();

        switch ($driver) {
            case 'mysql':
                // MySQL full-text search
                $query->whereRaw('MATCH(title, description) AGAINST(? IN BOOLEAN MODE)', [$searchText . '*']);
                break;

            case 'pgsql':
                // PostgreSQL full-text search
                $searchTermFormatted = implode(' & ', array_filter(explode(' ', $searchText)));
                $query->whereRaw("to_tsvector('english', title || ' ' || description) @@ to_tsquery('english', ?)", [$searchTermFormatted]);
                break;

            case 'sqlsrv':
                // SQL Server full-text search
                $query->whereRaw("CONTAINS((title, description), ?)", [$searchText]);
                break;

            case 'sqlite':
                if ($this->isFtsWorking()) {
                    try {
                        $searchTerm = $searchText . '*';
                        $ftsMatches = \DB::select("SELECT rowid FROM listings_fts WHERE listings_fts MATCH ?", [$searchTerm]);

                        if (count($ftsMatches) > 0) {
                            $ftsIds = array_map(function ($match) {
                                return $match->rowid;
                            }, $ftsMatches);

                            $query->whereIn('id', $ftsIds);
                            \Log::info("Using SQLite FTS for search: " . $searchText);
                            return;
                        }
                    } catch (\Exception $e) {
                        \Log::info("SQLite FTS search failed: " . $e->getMessage());
                    }
                }

                // Fallback to LIKE search
                $this->applyLikeSearch($query, $searchText);
                break;

            default:
                // Default to LIKE search for other drivers
                $this->applyLikeSearch($query, $searchText);
                break;
        }
    }

    /**
     * Apply LIKE-based search
     */
    private function applyLikeSearch($query, $searchText)
    {
        $words = preg_split('/\s+/', $searchText, -1, PREG_SPLIT_NO_EMPTY);

        $query->where(function ($q) use ($words) {
            foreach ($words as $word) {
                $term = '%' . $word . '%';
                $q->where(function ($innerQ) use ($term) {
                    $innerQ->where('title', 'LIKE', $term)
                        ->orWhere('description', 'LIKE', $term);
                });
            }
        });

        \Log::info("Using LIKE search for: " . $searchText);
    }

    /**
     * Apply standard filters (products, countries, manufacturers, user)
     */
    private function applyStandardFilters($query, $products, $countries, $manufacturers, $userId)
    {
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

        if ($userId) {
            $query->where('user_id', $userId);
        }
    }

    /**
     * Apply sorting based on the sort parameter
     */
    private function applySorting($query, $sort)
    {
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
    }

    /**
     * Test if SQLite FTS is working properly
     * 
     * @return bool
     */
    private function isFtsWorking()
    {
        try {
            // Check if FTS5 is available
            \DB::select('SELECT fts5(?)', ['test']);

            // Setup FTS table
            $this->setupSqliteFTS();

            // Try a simple search to verify it works
            $testQuery = \DB::select("SELECT rowid FROM listings_fts WHERE listings_fts MATCH ?", ['test*']);

            // If we get here without exceptions, FTS is working
            return true;
        } catch (\Exception $e) {
            \Log::error("FTS not working: " . $e->getMessage());
            return false;
        }
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