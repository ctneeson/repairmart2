<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\DeliveryMethod;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreQuoteRequest;
use App\Models\Quote;

class QuoteController extends Controller
{
    /**
     * Show the form for creating a new quote.
     *
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function create(Listing $listing)
    {
        // Make sure the user isn't trying to quote their own listing
        if ($listing->user_id === auth()->id()) {
            return redirect()->route('listings.show', $listing->id)
                ->with('error', 'You cannot create a quote for your own listing.');
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
                'details' => $validated['details'] ?? null,
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

                    // Create the attachment record
                    $quote->attachments()->create([
                        'path' => $path,
                        'filename' => $file->getClientOriginalName(),
                        'position' => $i + 1,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize()
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
        // Check if the authenticated user is either the customer or the repair specialist
        if ($quote->customer->id !== auth()->id() && $quote->repairSpecialist->id !== auth()->id()) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to view this quote.');
        }

        return view('quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing the specified quote.
     *
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function edit(Quote $quote)
    {
        // Check if the authenticated user is either the customer or the repair specialist
        if ($quote->customer->id !== auth()->id() && $quote->repairSpecialist->id !== auth()->id()) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to edit this quote.');
        }

        // Get all delivery methods
        $deliveryMethods = DeliveryMethod::all();

        return view('quotes.edit', compact('quote', 'deliveryMethods'));
    }

    /**
     * Update the specified quote in storage.
     *
     * @param  \App\Http\Requests\StoreQuoteRequest  $request
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function update(StoreQuoteRequest $request, Quote $quote)
    {
        // Check if the authenticated user is either the customer or the repair specialist
        if ($quote->customer->id !== auth()->id() && $quote->repairSpecialist->id !== auth()->id()) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to update this quote.');
        }

        $validated = $request->validated();

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update the quote
            $quote->update([
                'deliverymethod_id' => $validated['deliverymethod_id'],
                'amount' => $validated['amount'],
                'turnaround' => $validated['turnaround'],
                'details' => $validated['details'] ?? null,
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

                    // Create the attachment record
                    $quote->attachments()->create([
                        'path' => $path,
                        'filename' => $file->getClientOriginalName(),
                        'position' => $i + 1,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('quotes.show', $quote->id)
                ->with('success', 'Quote updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Failed to update quote: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update quote: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified quote from storage.
     *
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Quote $quote)
    {
        // Check if the authenticated user is either the customer or the repair specialist
        if ($quote->customer->id !== auth()->id() && $quote->repairSpecialist->id !== auth()->id()) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to delete this quote.');
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Soft delete the quote
            $quote->delete();

            DB::commit();

            return redirect()->route('quotes.index')
                ->with('success', 'Quote deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Failed to delete quote: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to delete quote: ' . $e->getMessage());
        }
    }
}
