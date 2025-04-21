<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\Quote;
use App\Models\Order;
use App\Models\ListingStatus;
use App\Models\QuoteStatus;
use App\Models\OrderStatus;
use App\Models\Email;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get unread messages count and details
        $unreadMessagesCount = $user->emailsReceived()
            ->whereNull('read_at')
            ->count();

        $unreadMessages = $user->emailsReceived()
            ->whereNull('read_at')
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'messages_page');

        // Initialize variables for listings
        $openListingsCount = 0;
        $openListings = collect();

        if ($user->hasRole('customer')) {
            // Get open status ID
            $openStatusId = ListingStatus::where('name', 'Open')->first()->id ?? null;

            if ($openStatusId) {
                // Get count of open listings
                $openListingsCount = Listing::where('user_id', $user->id)
                    ->where('status_id', $openStatusId)
                    ->count();

                // Get open listings with details
                $openListings = Listing::where('user_id', $user->id)
                    ->where('status_id', $openStatusId)
                    ->withCount('quotes')
                    ->orderBy('created_at', 'desc') // Order by created date
                    ->paginate(5, ['*'], 'listings_page');
            }
        }

        // Get quote counts by status (for specialists)
        $quotesByStatus = collect();
        $openQuotes = collect();

        if ($user->hasRole('specialist')) {
            $quotesByStatus = Quote::where('specialist_id', $user->id)
                ->select('status_id', DB::raw('count(*) as count'))
                ->groupBy('status_id')
                ->get()
                ->map(function ($item) {
                    $status = QuoteStatus::find($item->status_id);
                    return [
                        'status_name' => $status ? $status->name : 'Unknown',
                        'count' => $item->count,
                        'status_id' => $item->status_id
                    ];
                });

            // Get open quotes with details
            $openStatusId = QuoteStatus::where('name', 'Open')->first()->id ?? null;
            if ($openStatusId) {
                $openQuotes = Quote::where('specialist_id', $user->id)
                    ->where('status_id', $openStatusId)
                    ->with('listing')
                    ->orderBy('created_at', 'desc')
                    ->paginate(5, ['*'], 'quotes_page');
            }
        }

        // Get orders where user is customer, grouped by status
        $customerOrdersByStatus = Order::where('customer_id', $user->id)
            ->select('status_id', DB::raw('count(*) as count'))
            ->groupBy('status_id')
            ->get()
            ->map(function ($item) {
                $status = OrderStatus::find($item->status_id);
                return [
                    'status_name' => $status ? $status->name : 'Unknown',
                    'count' => $item->count,
                    'status_id' => $item->status_id
                ];
            });

        // Get active customer orders with details
        $closedStatusId = OrderStatus::where('name', 'Closed')->first()->id ?? null;
        $activeCustomerOrders = Order::where('customer_id', $user->id)
            ->when($closedStatusId, function ($query) use ($closedStatusId) {
                return $query->where('status_id', '!=', $closedStatusId);
            })
            ->with(['listing', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'customer_orders_page');

        // Get orders where user is specialist, grouped by status
        $specialistOrdersByStatus = Order::where('specialist_id', $user->id)
            ->select('status_id', DB::raw('count(*) as count'))
            ->groupBy('status_id')
            ->get()
            ->map(function ($item) {
                $status = OrderStatus::find($item->status_id);
                return [
                    'status_name' => $status ? $status->name : 'Unknown',
                    'count' => $item->count,
                    'status_id' => $item->status_id
                ];
            });

        // Get active specialist orders with details
        $activeSpecialistOrders = Order::where('specialist_id', $user->id)
            ->when($closedStatusId, function ($query) use ($closedStatusId) {
                return $query->where('status_id', '!=', $closedStatusId);
            })
            ->with(['listing', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'specialist_orders_page');

        return view('home.dashboard', compact(
            'unreadMessagesCount',
            'unreadMessages',
            'openListingsCount',
            'openListings',
            'quotesByStatus',
            'openQuotes',
            'customerOrdersByStatus',
            'activeCustomerOrders',
            'specialistOrdersByStatus',
            'activeSpecialistOrders'
        ));
    }
}
