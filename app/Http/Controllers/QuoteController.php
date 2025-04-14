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

class QuoteController extends Controller
{
    /**
     * Display a listing of the quotes.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Check if the user can view quotes
        if (!Gate::allows('viewAny', Quote::class)) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to view quotes.');
        }

        $user = auth()->user();

        // For customer role: Get quotes for listings created by the current user
        $receivedQuotes = collect();
        $receivedPendingCount = 0;

        if ($user->hasRole('customer')) {
            $receivedQuotes = Quote::whereHas('listing', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['listing', 'status', 'deliveryMethod', 'currency'])
                ->latest()
                ->paginate(10, ['*'], 'received_page');

            $receivedPendingCount = Quote::whereHas('listing', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->whereHas('status', function ($query) {
                $query->where('name', 'Open');
            })->count();
        }

        // For specialist role: Get quotes created by the current user
        $submittedQuotes = collect();
        $submittedOpenCount = 0;

        if ($user->hasRole('specialist')) {
            $submittedQuotes = Quote::where('user_id', $user->id)
                ->with(['listing', 'status', 'deliveryMethod', 'currency'])
                ->latest()
                ->paginate(10, ['*'], 'submitted_page');

            $submittedOpenCount = Quote::where('user_id', $user->id)
                ->whereHas('status', function ($query) {
                    $query->where('name', 'Open');
                })->count();
        }

        return view('quotes.index', compact(
            'receivedQuotes',
            'receivedPendingCount',
            'submittedQuotes',
            'submittedOpenCount'
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
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function store(StoreQuoteRequest $request)
    {
        $validated = $request->validated();

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the quote
            $quote = Quote::create([
                'user_id' => auth()->id(),
                'listing_id' => $validated['listing_id'],
                'status_id' => 1, // Default: Open
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
            $listingOwner = $listing->user;

            // You can implement notification here
            // Notification::send($listingOwner, new NewQuoteNotification($quote));

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

        // Get all delivery methods
        $deliveryMethods = DeliveryMethod::all();

        return view('quotes.edit', compact('quote', 'deliveryMethods'));
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

        // Delete all associated attachments
        foreach ($quote->attachments as $attachment) {
            // Delete the file from storage
            Storage::disk('public')->delete($attachment->path);
            // Delete the attachment record
            $attachment->delete();
        }

        $quote->delete();

        return redirect()->route('quotes.index')
            ->with('success', 'Quote deleted successfully.');
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
