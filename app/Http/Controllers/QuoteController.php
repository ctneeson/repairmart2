<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\DeliveryMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;
use Illuminate\Support\Facades\Gate;
use App\Mail\QuoteReceived;
use App\Mail\QuoteUpdated;

class QuoteController extends Controller
{
    /**
     * Display a listing of the quotes, optionally filtered by listing_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', Quote::class)) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to view quotes.');
        }

        $user = auth()->user();
        $listingId = $request->input('listing_id');
        \Log::info('Initial listing ID: ' . $listingId);

        // First, verify the listing exists before doing anything else
        $filterListing = null;
        if ($listingId) {
            $filterListing = Listing::find($listingId);

            if (!$filterListing) {
                \Log::warning('Listing not found: ' . $listingId);
                return redirect()->route('quotes.index')
                    ->with('error', 'The specified listing does not exist.');
            }

            \Log::info('Filter Listing found: ' . $filterListing->title);
        }

        $defaultTab = $user->hasRole('specialist') && !$user->hasRole('customer')
            ? 'submitted' : 'received';
        $activeTab = $request->input('tab', $defaultTab);

        \Log::info('Active Tab: ' . $activeTab);
        \Log::info('Request Tab: ' . $request->input('tab'));
        \Log::info('Default Tab: ' . $defaultTab);

        // Only force tab change if the user doesn't have the required role AND has the alternative role
        if ($activeTab === 'received' && !$user->hasRole('customer') && $user->hasRole('specialist')) {
            $activeTab = 'submitted';
        }

        if ($activeTab === 'submitted' && !$user->hasRole('specialist') && $user->hasRole('customer')) {
            $activeTab = 'received';
        }

        // For customer role: Get quotes for listings created by the current user
        $receivedQuotes = collect();
        $receivedPendingCount = 0;

        if ($user->hasRole('customer')) {
            // Build the base query for received quotes
            $receivedQuery = Quote::whereHas('listing', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

            if ($listingId) {
                $receivedQuery->where('listing_id', $listingId);

                // Only verify ownership if the user is accessing as a customer
                if ($activeTab === 'received') {
                    // Verify the listing belongs to the user
                    $listingExists = Listing::where('id', $listingId)
                        ->where('user_id', $user->id)
                        ->exists();

                    if (!$listingExists) {
                        return redirect()->route('quotes.index')
                            ->with('error', 'You do not have permission to view quotes for this listing.');
                    }
                }
            }

            $receivedQuotes = $receivedQuery
                ->with(['listing', 'status', 'deliveryMethod', 'currency', 'customer'])
                ->latest()
                ->paginate(10, ['*'], 'received_page')
                ->appends([
                    'listing_id' => $listingId,
                    'tab' => $activeTab
                ]);

            $pendingQuery = Quote::whereHas('listing', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->whereHas('status', function ($query) {
                $query->where('name', 'Open');
            });

            if ($listingId) {
                $pendingQuery->where('listing_id', $listingId);
            }

            $receivedPendingCount = $pendingQuery->count();
        }

        // For specialist role: Get quotes created by the current user
        $submittedQuotes = collect();
        $submittedOpenCount = 0;

        if ($user->hasRole('specialist')) {
            $submittedQuery = Quote::where('user_id', $user->id);
            \Log::info("Specialist section - Listing ID: " . $listingId);

            if ($listingId) {
                $submittedQuery->where('listing_id', $listingId);
                \Log::info("Submitted Query with Listing ID: " . $listingId);

            }

            $submittedQuotes = $submittedQuery
                ->with(['listing', 'status', 'deliveryMethod', 'currency'])
                ->latest()
                ->paginate(10, ['*'], 'submitted_page')
                ->appends([
                    'listing_id' => $listingId,
                    'tab' => $activeTab
                ]);

            $openSubmittedQuery = Quote::where('user_id', $user->id)
                ->whereHas('status', function ($query) {
                    $query->where('name', 'Open');
                });

            if ($listingId) {
                $openSubmittedQuery->where('listing_id', $listingId);
            }

            $submittedOpenCount = $openSubmittedQuery->count();
        }

        return view('quotes.index', compact(
            'receivedQuotes',
            'receivedPendingCount',
            'submittedQuotes',
            'submittedOpenCount',
            'filterListing',
            'activeTab'
        ));
    }

    /**
     * Show the form for creating a new quote.
     *
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function create(Listing $listing)
    {
        // Use Gate to check if the user can create a quote for this listing
        $response = Gate::inspect('create', [Quote::class, $listing]);

        if (!$response->allowed()) {
            return redirect()->route('listings.show', $listing->id)
                ->with('error', $response->message());
        }

        // Get the authenticated user for pre-filling the form
        $user = auth()->user();

        // Get all delivery methods
        $deliveryMethods = DeliveryMethod::all();

        return view('quotes.create', compact('listing', 'user', 'deliveryMethods'));
    }

    /**
     * Store a newly created quote in storage.
     *
     * @param  \App\Http\Requests\StoreQuoteRequest  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function store(StoreQuoteRequest $request)
    {
        $validated = $request->validated();

        // Check if validation found an existing quote
        if (isset($validated['existing_quote_id'])) {
            return redirect()->route('quotes.edit', $validated['existing_quote_id'])
                ->with('warning', 'You already have an open quote with the same details for this listing. You can edit it below instead of creating a duplicate.');
        }

        DB::beginTransaction();

        $openStatusId = \DB::table('quote_statuses')->where('name', 'Open')->value('id');

        try {
            // Create the quote
            $quote = Quote::create([
                'user_id' => auth()->id(),
                'listing_id' => $validated['listing_id'],
                'status_id' => $openStatusId, // Default: Open
                'currency_id' => $validated['currency_id'],
                'deliverymethod_id' => $validated['deliverymethod_id'],
                'amount' => $validated['amount'],
                'turnaround' => $validated['turnaround'],
                'description' => $validated['description'] ?? null,
                'use_default_location' => $validated['use_default_location'],
                'address_line1' => $validated['address_line1'],
                'address_line2' => $validated['address_line2'] ?? null,
                'city' => $validated['city'],
                'postcode' => $validated['postcode'],
                'country_id' => $validated['country_id'],
                'phone' => $validated['phone']
            ]);

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $i => $file) {
                    $path = $file->store('attachments/quotes', 'public');

                    // Create the attachment record with user_id
                    $quote->attachments()->create([
                        'path' => $path,
                        'position' => $i + 1,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'user_id' => auth()->id()  // Add the user ID
                    ]);
                }
            }

            // Send notification to the listing owner
            $listing = Listing::find($validated['listing_id']);
            $listingOwner = $listing->customer;

            try {
                \Mail::to($listingOwner->email)
                    ->send(new QuoteReceived($quote, $listingOwner));
                // ->queue(new QuoteReceived($quote, $listingOwner));
                \Log::info("Quote notification email sent to {$listingOwner->email} for quote #{$quote->id}");
            } catch (\Exception $e) {
                \Log::error("Failed to send quote notification email: " . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('quotes.show', $quote->id)
                ->with('success', 'Quote created successfully! The customer has been notified.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Failed to create quote: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create quote: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified quote.
     *
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function show(Quote $quote)
    {
        // Use Gate to check if the user can view this quote
        $response = Gate::inspect('view', $quote);

        if (!$response->allowed()) {
            return redirect()->route('home')
                ->with('error', $response->message());
        }

        return view('quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing a quote.
     *
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function edit(Quote $quote)
    {
        // Use Gate to check if the user can update this quote
        $response = Gate::inspect('update', $quote);

        if (!$response->allowed()) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', $response->message());
        }

        $user = auth()->user();

        // Get all delivery methods
        $deliveryMethods = DeliveryMethod::all();

        return view('quotes.edit', compact('quote', 'user', 'deliveryMethods'));
    }

    /**
     * Update the specified quote in storage.
     *
     * @param  \App\Http\Requests\UpdateQuoteRequest  $request
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function update(UpdateQuoteRequest $request, Quote $quote)
    {
        // Use Gate to check if the user can update this quote
        $response = Gate::inspect('update', $quote);

        if (!$response->allowed()) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', $response->message());
        }

        $validated = $request->validated();
        $listingOwner = $quote->customer;

        // Handle the default location toggle
        if ($validated['use_default_location']) {
            // If using default location, get current user's address data
            $user = auth()->user();
            $validated['address_line1'] = $user->address_line1;
            $validated['address_line2'] = $user->address_line2;
            $validated['city'] = $user->city;
            $validated['postcode'] = $user->postcode;
            $validated['country_id'] = $user->country_id;
        }

        $quote->update($validated);

        try {
            \Mail::to($listingOwner->email)
                ->send(new QuoteUpdated($quote, $listingOwner));
            // ->queue(new QuoteUpdated($quote, $listingOwner));
            \Log::info("Update notification email sent to {$listingOwner->email} for quote #{$quote->id}");
        } catch (\Exception $e) {
            \Log::error("Failed to send update notification email: " . $e->getMessage());
        }

        return redirect()->route('quotes.show', $quote->id)
            ->with('success', 'Quote updated successfully.');
    }

    /**
     * Remove the specified quote from storage.
     *
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Quote $quote)
    {
        // Use Gate to check if the user can delete this quote
        $response = Gate::inspect('delete', $quote);

        if (!$response->allowed()) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', $response->message());
        }

        $openStatusId = \DB::table('quote_statuses')->where('name', 'Open')->value('id');

        if ($quote->status_id != $openStatusId) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'Only open quotes can be retracted.');
        }

        $retractedStatusId = \DB::table('quote_statuses')->where('name', 'Closed-Retracted')->value('id');

        if (!$retractedStatusId) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'Quote status "Closed-Retracted" not found.');
        }

        DB::beginTransaction();

        try {
            // Update the quote status to 'Closed-Retracted' before deleting
            $quote->status_id = $retractedStatusId;
            $quote->save();

            // Delete all associated attachments
            foreach ($quote->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }

            $quote->delete();

            \Log::info('Quote #' . $quote->id . ' marked as Closed-Retracted and soft-deleted by user #' . auth()->id());

            DB::commit();

            return redirect()->route('quotes.index')
                ->with('success', 'Quote retracted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Failed to retract quote: ' . $e->getMessage());

            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'Failed to retract quote: ' . $e->getMessage());
        }
    }

    /**
     * Show the attachments management page for a quote.
     *
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function attachments(Quote $quote)
    {
        // Use Gate to check if the user can update this quote (same permissions for attachments)
        $response = Gate::inspect('update', $quote);

        if (!$response->allowed()) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', $response->message());
        }

        return view('quotes.attachments', compact('quote'));
    }

    /**
     * Update the attachments for a quote.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function updateAttachments(Request $request, Quote $quote)
    {
        // Use Gate to check if the user can update this quote
        $response = Gate::inspect('update', $quote);

        if (!$response->allowed()) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', $response->message());
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Handle attachment positioning/order updates
            if ($request->has('positions')) {
                foreach ($request->positions as $id => $position) {
                    $quote->attachments()->where('id', $id)->update(['position' => $position]);
                }
            }

            // Handle attachment deletions
            if ($request->has('delete_attachments')) {
                foreach ($request->delete_attachments as $id) {
                    $attachment = $quote->attachments()->find($id);

                    if ($attachment) {
                        // Delete the file from storage
                        Storage::disk('public')->delete($attachment->path);
                        // Delete the attachment record
                        $attachment->delete();
                    }
                }
            }

            // Handle new attachment uploads
            if ($request->hasFile('new_attachments')) {
                $highestPosition = $quote->attachments()->max('position') ?? 0;

                foreach ($request->file('new_attachments') as $i => $file) {
                    $path = $file->store('attachments/quotes', 'public');

                    // Create the attachment record
                    $quote->attachments()->create([
                        'path' => $path,
                        'position' => $highestPosition + $i + 1,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'user_id' => auth()->id()  // Add the user ID
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('quotes.show', $quote->id)
                ->with('success', 'Quote attachments updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Failed to update quote attachments: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to update attachments: ' . $e->getMessage());
        }
    }

    /**
     * Add attachments to a quote.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function addAttachments(Request $request, Quote $quote)
    {
        // Use Gate to check if the user can update this quote
        $response = Gate::inspect('update', $quote);

        if (!$response->allowed()) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', $response->message());
        }

        // Get attachments from request
        $attachments = $request->file('attachments') ?? [];

        // Get max position from existing attachments
        $position = $quote->attachments()->max('position') ?? 0;
        foreach ($attachments as $attachment) {
            $path = $attachment->store('attachments', 'public');
            $quote->attachments()->create([
                'path' => $path,
                'position' => $position + 1,
                'mime_type' => $attachment->getMimeType(),
                'user_id' => auth()->id()  // Add the user ID
            ]);
            $position++;
        }
        return redirect()->back()->with('success', 'Attachments added successfully.');
    }
}
