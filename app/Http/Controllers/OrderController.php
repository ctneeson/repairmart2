<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Get customer orders if user has customer role
        $customerOrders = collect([]);
        if ($user->hasRole('customer')) {
            $customerOrders = Order::with(['quote.listing', 'status', 'repairSpecialist', 'currency'])
                ->where('customer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'customer_page');
        }

        // Get specialist orders if user has specialist role
        $specialistOrders = collect([]);
        if ($user->hasRole('specialist')) {
            $specialistOrders = Order::with(['quote.listing', 'status', 'customer', 'currency'])
                ->where('specialist_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'specialist_page');
        }

        return view('orders.index', compact('customerOrders', 'specialistOrders'));
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
    public function store(Request $request, Quote $quote)
    {
        // Add some debugging to see what's happening
        \Log::info('Order store method called');
        \Log::info('Request data:', $request->all());

        // Validation
        $validated = $request->validate([
            'comment' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        \Log::info('Validation passed');

        // Authorization check
        if (!auth()->user()->can('create', [Order::class, $quote])) {
            return redirect()->route('quotes.show', $quote->id)
                ->with('error', 'You are not authorized to create this order.');
        }

        \Log::info('Authorization passed');

        // Start DB transaction
        DB::beginTransaction();

        try {
            // Create the order with default values
            $order = Order::create([
                'quote_id' => $quote->id,
                'customer_id' => $quote->listing->user_id,
                'specialist_id' => $quote->specialist_id,
                'status_id' => 1, // "Open" status
                'override_quote' => false,
                'amount' => $quote->amount,
                'currency_id' => $quote->currency_id,
            ]);

            \Log::info('Order created with ID: ' . $order->id);

            // Add system comment about order creation
            $order->comments()->create([
                'user_id' => auth()->id(),
                'comment' => 'Order created with status: Created',
            ]);

            \Log::info('System comment added');

            // Add customer comment if provided
            if ($request->filled('comment')) {
                $order->comments()->create([
                    'user_id' => auth()->id(),
                    'comment' => $request->comment,
                ]);
                \Log::info('User comment added');
            }

            // Handle attachments
            if ($request->hasFile('attachments')) {
                $position = 1; // Start position counter

                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments/orders', 'public');

                    // Create the attachment record
                    $order->attachments()->create([
                        'path' => $path,
                        'position' => $position++,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'user_id' => auth()->id()
                    ]);
                }

                \Log::info('Attachments processed: ' . ($position - 1) . ' files');
            }

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            // Log the error
            \Log::error('Failed to create order: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Failed to create order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified order.
     *
     * @param Order $order
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Order $order)
    {
        // Authorization check
        if (!auth()->user()->can('view', $order)) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view this order.');
        }

        // Get feedback types for the feedback form (if order is closed)
        $feedbackTypes = [];
        if ($order->status_id == 7) { // Closed status
            $feedbackTypes = \App\Models\FeedbackType::all();
        }

        return view('orders.show', compact('order', 'feedbackTypes'));
    }

    /**
     * Update the status of an order.
     *
     * @param \Illuminate\Http\Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Validate request
        $validated = $request->validate([
            'status_id' => 'required|exists:order_statuses,id',
        ]);

        // Authorization check
        if (!auth()->user()->can('updateStatus', $order)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'You are not authorized to update this order status.');
        }

        // Check if the transition is allowed for the user's role
        $userRole = auth()->user()->roles()->first()->id;
        $isValidTransition = \App\Models\OrderStatusTransition::where([
            'role_id' => $userRole,
            'from_status_id' => $order->status_id,
            'to_status_id' => $request->status_id,
        ])->exists();

        if (!$isValidTransition) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This status transition is not allowed.');
        }

        // Get status names for comment
        $oldStatus = \App\Models\OrderStatus::find($order->status_id);
        $newStatus = \App\Models\OrderStatus::find($request->status_id);

        DB::beginTransaction();

        try {
            // Update the order status
            $order->status_id = $request->status_id;
            $order->save();

            // Add a comment about the status change automatically
            $statusChangeComment = sprintf(
                'Status changed from "%s" to "%s"',
                $oldStatus->name,
                $newStatus->name
            );

            $order->comments()->create([
                'user_id' => auth()->id(),
                'comment' => $statusChangeComment,
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Failed to update order status: ' . $e->getMessage());

            return redirect()->route('orders.show', $order)
                ->with('error', 'Failed to update order status: ' . $e->getMessage());
        }
    }

    /**
     * Update the order amount (for price adjustment).
     *
     * @param \Illuminate\Http\Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAmount(Request $request, Order $order)
    {
        // Validate request
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Authorization check
        if (!auth()->user()->can('updateAmount', $order)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'You are not authorized to update this order amount.');
        }

        // Check if amount can be updated in the current status
        if (!$order->isAmountEditable()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Order amount can only be updated when in a status that allows price adjustments.');
        }

        $oldAmount = $order->amount;

        DB::beginTransaction();

        try {
            // Update the order amount
            $order->amount = $request->amount;
            $order->save();

            // Add a comment about the amount change
            $order->comments()->create([
                'user_id' => auth()->id(),
                'comment' => sprintf(
                    'Order amount updated from %s %s to %s %s',
                    $order->currency->iso_code,
                    number_format($oldAmount, 2),
                    $order->currency->iso_code,
                    number_format($request->amount, 2)
                ),
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order amount updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Failed to update order amount: ' . $e->getMessage());

            return redirect()->route('orders.show', $order)
                ->with('error', 'Failed to update order amount: ' . $e->getMessage());
        }
    }

    /**
     * Add feedback to the order.
     *
     * @param \Illuminate\Http\Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addFeedback(Request $request, Order $order)
    {
        // Validate request
        $validated = $request->validate([
            'feedback_type_id' => 'required|exists:feedback_types,id',
            'feedback' => 'required|string|max:255',
            'feedback_type' => 'required|in:customer,specialist',
        ]);

        // Check if the order is in Closed status (id = 7)
        if ($order->status_id != 7) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Feedback can only be added when the order is closed.');
        }

        // Check if the user is authorized to add feedback
        $isCustomer = auth()->id() === $order->customer_id;
        $isSpecialist = auth()->id() === $order->specialist_id;

        if (
            ($request->feedback_type == 'customer' && !$isCustomer) ||
            ($request->feedback_type == 'specialist' && !$isSpecialist)
        ) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'You are not authorized to add this type of feedback.');
        }

        DB::beginTransaction();

        try {
            // Update the order with feedback
            if ($request->feedback_type == 'customer') {
                if (!empty($order->customer_feedback_id)) {
                    return redirect()->route('orders.show', $order)
                        ->with('error', 'Customer feedback has already been provided.');
                }

                $order->customer_feedback_id = $request->feedback_type_id;
                $order->customer_feedback = $request->feedback;
            } else {
                if (!empty($order->specialist_feedback_id)) {
                    return redirect()->route('orders.show', $order)
                        ->with('error', 'Specialist feedback has already been provided.');
                }

                $order->specialist_feedback_id = $request->feedback_type_id;
                $order->specialist_feedback = $request->feedback;
            }

            $order->save();

            // Add a comment about the feedback
            $order->comments()->create([
                'user_id' => auth()->id(),
                'comment' => sprintf(
                    '%s feedback added: %s',
                    ucfirst($request->feedback_type),
                    $request->feedback
                ),
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Feedback added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Failed to add feedback: ' . $e->getMessage());

            return redirect()->route('orders.show', $order)
                ->with('error', 'Failed to add feedback: ' . $e->getMessage());
        }
    }

    /**
     * Add a comment to an order.
     *
     * @param \Illuminate\Http\Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeComment(Request $request, Order $order)
    {
        // Validate request
        $validated = $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        // Check if the user is authorized to add a comment
        $isCustomer = auth()->id() === $order->customer_id;
        $isSpecialist = auth()->id() === $order->specialist_id;

        if (!$isCustomer && !$isSpecialist) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'You are not authorized to add comments to this order.');
        }

        // Check if the order is closed
        if ($order->status_id == 7) { // Closed status
            return redirect()->route('orders.show', $order)
                ->with('error', 'Comments cannot be added to closed orders.');
        }

        try {
            // Create the comment
            $order->comments()->create([
                'user_id' => auth()->id(),
                'comment' => $request->comment,
            ]);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Comment added successfully.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to add comment: ' . $e->getMessage());

            return redirect()->route('orders.show', $order)
                ->with('error', 'Failed to add comment: ' . $e->getMessage());
        }
    }

    /**
     * Add an attachment to an order.
     *
     * @param \Illuminate\Http\Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAttachment(Request $request, Order $order)
    {
        // Validate request
        $validated = $request->validate([
            'attachment' => 'required|file|max:10240', // 10MB max
        ]);

        // Check if the user is authorized to add an attachment
        $isCustomer = auth()->id() === $order->customer_id;
        $isSpecialist = auth()->id() === $order->specialist_id;

        if (!$isCustomer && !$isSpecialist) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'You are not authorized to add attachments to this order.');
        }

        // Check if the order is closed
        if ($order->status_id == 7) { // Closed status
            return redirect()->route('orders.show', $order)
                ->with('error', 'Attachments cannot be added to closed orders.');
        }

        try {
            // Get the file
            $file = $request->file('attachment');
            $path = $file->store('attachments/orders', 'public');

            // Get the next position
            $position = $order->attachments()->count() + 1;

            // Create the attachment
            $attachment = $order->attachments()->create([
                'user_id' => auth()->id(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'position' => $position,
            ]);

            // Add a comment about the attachment
            $order->comments()->create([
                'user_id' => auth()->id(),
                'comment' => 'Added attachment: ' . $file->getClientOriginalName(), // Use original name in comment only
            ]);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Attachment added successfully.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to add attachment: ' . $e->getMessage());

            return redirect()->route('orders.show', $order)
                ->with('error', 'Failed to add attachment: ' . $e->getMessage());
        }
    }

    /**
     * Download an attachment
     */
    public function downloadAttachment(Order $order, $attachmentId)
    {
        $attachment = $order->attachments()->findOrFail($attachmentId);

        // Check if user is authorized to view this attachment
        $isCustomer = auth()->id() === $order->customer_id;
        $isSpecialist = auth()->id() === $order->specialist_id;
        $isAdmin = auth()->user()->hasRole('admin');

        if (!$isCustomer && !$isSpecialist && !$isAdmin) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'You are not authorized to download this attachment.');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($attachment->path)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Attachment file not found.');
        }

        // Extract filename from path
        $filename = basename($attachment->path);

        return Storage::disk('public')->download($attachment->path, $filename);
    }

    /**
     * Delete an attachment
     */
    public function deleteAttachment(Order $order, $attachmentId)
    {
        $attachment = $order->attachments()->findOrFail($attachmentId);

        // Check if user is authorized to delete this attachment
        $isOwner = auth()->id() === $attachment->user_id;
        $isAdmin = auth()->user()->hasRole('admin');

        if (!$isOwner && !$isAdmin) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'You can only delete attachments you uploaded.');
        }

        // Delete file from storage
        Storage::disk('public')->delete($attachment->path);

        // Delete attachment record
        $attachment->delete();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Attachment deleted successfully.');
    }

    /**
     * Update the attachments for an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function updateAttachments(Request $request, Order $order)
    {
        // Use Gate to check if the user can update this order
        $response = Gate::inspect('update', $order);

        if (!$response->allowed()) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', $response->message());
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Handle attachment positioning/order updates
            if ($request->has('positions')) {
                foreach ($request->positions as $id => $position) {
                    $order->attachments()->where('id', $id)->update(['position' => $position]);
                }
            }

            // Handle attachment deletions
            if ($request->has('delete_attachments')) {
                $unauthorizedDeletes = 0;
                foreach ($request->delete_attachments as $id) {
                    $attachment = $order->attachments()->find($id);

                    if ($attachment) {
                        // Check if the user is authorized to delete this attachment
                        $isOwner = auth()->id() === $attachment->user_id;
                        $isAdmin = auth()->user()->hasRole('admin');

                        if ($isOwner || $isAdmin) {
                            // Delete the file from storage
                            Storage::disk('public')->delete($attachment->path);
                            // Delete the attachment record
                            $attachment->delete();
                        } else {
                            // Count unauthorized deletion attempts
                            $unauthorizedDeletes++;
                        }
                    }
                }

                // If there were unauthorized deletion attempts, log and notify
                if ($unauthorizedDeletes > 0) {
                    \Log::warning("User " . auth()->id() . " attempted to delete {$unauthorizedDeletes} attachments they don't own");
                }
            }

            // Handle new attachment uploads
            if ($request->hasFile('new_attachments')) {
                $highestPosition = $order->attachments()->max('position') ?? 0;

                foreach ($request->file('new_attachments') as $i => $file) {
                    $path = $file->store('attachments/orders', 'public');

                    // Create the attachment record
                    $order->attachments()->create([
                        'path' => $path,
                        'position' => $highestPosition + $i + 1,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'user_id' => auth()->id()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order attachments updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Failed to update order attachments: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to update attachments: ' . $e->getMessage());
        }
    }

    /**
     * Show the attachments management page for an order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function attachments(Order $order)
    {
        // Use Gate to check if the user can update this quote (same permissions for attachments)
        $response = Gate::inspect('update', $order);

        if (!$response->allowed()) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', $response->message());
        }

        return view('orders.attachments', compact('order'));
    }

    /**
     * Add attachments to an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function addAttachments(Request $request, Order $order)
    {
        // Use Gate to check if the user can update this order
        $response = Gate::inspect('update', $order);

        if (!$response->allowed()) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', $response->message());
        }

        // Get attachments from request
        $attachments = $request->file('attachments') ?? [];

        // Get max position from existing attachments
        $position = $order->attachments()->max('position') ?? 0;
        foreach ($attachments as $attachment) {
            $path = $attachment->store('attachments', 'public');
            $order->attachments()->create([
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
