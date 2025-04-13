<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new order from a quote.
     *
     * @param Quote $quote
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Quote $quote)
    {
        // Use Gate to check if the user can create an order from this quote
        $response = Gate::inspect('create', [Order::class, $quote]);

        if (!$response->allowed()) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', $response->message());
        }

        return view('orders.create', compact('quote'));
    }

    /**
     * Store a newly created order in storage.
     *
     * @param StoreOrderRequest $request
     * @param Quote $quote
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function store(StoreOrderRequest $request, Quote $quote)
    {
        // Authorization check
        $response = Gate::inspect('create', [Order::class, $quote]);

        if (!$response->allowed()) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', $response->message());
        }

        // Start DB transaction
        DB::beginTransaction();

        try {
            // Create the order with default values
            $order = Order::create([
                'quote_id' => $quote->id,
                'customer_id' => $quote->listing->user_id,
                'status_id' => 1, // "Open" status
                'override_quote' => false,
                'amount' => null,
                'customer_feedback_id' => null,
                'customer_feedback' => null,
                'specialist_feedback_id' => null,
                'specialist_feedback' => null,
            ]);

            // Add system comment about order creation
            $order->comments()->create([
                'user_id' => 1, // System user
                'comment' => 'Order created with status: Created',
            ]);

            // Add customer comment if provided
            if ($request->filled('comment')) {
                $order->comments()->create([
                    'user_id' => auth()->id(),
                    'comment' => $request->comment,
                ]);
            }

            // Handle attachments
            if ($request->hasFile('attachments')) {
                $position = 1; // Start position counter

                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments/orders', 'public');

                    // Create the attachment record
                    $order->attachments()->create([
                        'path' => $path,
                        'filename' => $file->getClientOriginalName(),
                        'position' => $position++,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            // Log the error
            \Log::error('Failed to create order: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to create order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
