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

class QuoteController extends Controller
{
    /**
     * Display a listing of the quotes.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View
     */
    public function index()
    {
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
        $user = auth()->user();

        // 1. Check if the authenticated user is the listing owner
        $isListingOwner = $user->id === $quote->listing->user_id;

        // 2. Check if the authenticated user is the quote creator
        $isQuoteCreator = $user->id === $quote->user_id;

        // 3. Check if the authenticated user is an admin
        $isAdmin = $user->roles->where('name', 'admin')->count() > 0;

        // If user doesn't have any of the required roles, redirect with error
        if (!$isListingOwner && !$isQuoteCreator && !$isAdmin) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to view this quote.');
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
        // Check if user is authorized to edit this quote
        $user = auth()->user();
        $isQuoteCreator = $user->id === $quote->user_id;
        $isAdmin = $user->roles->where('name', 'admin')->count() > 0;

        if (!$isQuoteCreator && !$isAdmin) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'You do not have permission to edit this quote.');
        }

        // If the quote status is not "Open", prevent editing
        if ($quote->status->name !== 'Open' && !$isAdmin) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'This quote cannot be edited because its status is not "Open".');
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
        // Check if user is authorized to update this quote
        $user = auth()->user();
        $isQuoteCreator = $user->id === $quote->user_id;
        $isAdmin = $user->roles->where('name', 'admin')->count() > 0;

        if (!$isQuoteCreator && !$isAdmin) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'You do not have permission to update this quote.');
        }

        // If the quote status is not "Open", prevent updating
        if ($quote->status->name !== 'Open' && !$isAdmin) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'This quote cannot be updated because its status is not "Open".');
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
        // Check if user is authorized to delete this quote
        $user = auth()->user();
        $isQuoteCreator = $user->id === $quote->user_id;
        $isAdmin = $user->roles->where('name', 'admin')->count() > 0;

        if (!$isQuoteCreator && !$isAdmin) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'You do not have permission to delete this quote.');
        }

        // If the quote status is not "Open", prevent deletion for non-admins
        if ($quote->status->name !== 'Open' && !$isAdmin) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'This quote cannot be deleted because its status is not "Open".');
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
        // Check if user is authorized to manage attachments for this quote
        $user = auth()->user();
        $isQuoteCreator = $user->id === $quote->user_id;
        $isAdmin = $user->roles->where('name', 'admin')->count() > 0;

        if (!$isQuoteCreator && !$isAdmin) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'You do not have permission to manage attachments for this quote.');
        }

        return view('quotes.attachments', compact('quote'));
    }
}
