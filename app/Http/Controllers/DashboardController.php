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
use App\Models\User;
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

        // Get activity data
        $activityData = $this->getActivityData($user);

        // Get messaging data
        $messageData = $this->getMessageData($user);

        // Get listings data (for customers)
        $listingData = $this->getListingData($user);

        // Get quote data (for specialists)
        $quoteData = $this->getQuoteData($user);

        // Get order data
        $orderData = $this->getOrderData($user);

        return view('home.dashboard', array_merge(
            ['activityData' => $activityData],
            $messageData,
            $listingData,
            $quoteData,
            $orderData
        ));
    }
    /**
     * Get activity summary data for the dashboard.
     *
     * @param \App\Models\User|null $user
     * @return array
     */
    private function getActivityData($user)
    {
        $activityData = [
            'customer' => [
                'listings_published' => [
                    '7' => 0,
                    '30' => 0,
                    '90' => 0
                ],
                'orders_closed' => [
                    '7' => 0,
                    '30' => 0,
                    '90' => 0
                ]
            ],
            'specialist' => [
                'quotes_submitted' => [
                    '7' => 0,
                    '30' => 0,
                    '90' => 0
                ],
                'orders_closed' => [
                    '7' => 0,
                    '30' => 0,
                    '90' => 0
                ]
            ]
        ];

        // Populate customer activity data
        if ($user->hasRole('customer')) {
            // Listings published in different periods
            foreach ([7, 30, 90] as $days) {
                $activityData['customer']['listings_published'][$days] = Listing::where('user_id', $user->id)
                    ->where('published_at', '>=', now()->subDays($days))
                    ->count();

                $activityData['customer']['orders_closed'][$days] = Order::where('customer_id', $user->id)
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'Closed');
                    })
                    ->where('updated_at', '>=', now()->subDays($days))
                    ->count();
            }
        }

        // Populate specialist activity data
        if ($user->hasRole('specialist')) {
            // Quotes submitted and orders closed in different periods
            foreach ([7, 30, 90] as $days) {
                $activityData['specialist']['quotes_submitted'][$days] = Quote::where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subDays($days))
                    ->count();

                $activityData['specialist']['orders_closed'][$days] = Order::where('specialist_id', $user->id)
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'Closed');
                    })
                    ->where('updated_at', '>=', now()->subDays($days))
                    ->count();
            }
        }

        return $activityData;
    }

    /**
     * Get messaging data for the dashboard.
     *
     * @param \App\Models\User|null $user
     * @return array
     */
    private function getMessageData($user)
    {
        // Get unread messages count
        $unreadMessagesCount = $user->emailsReceived()
            ->whereNull('read_at')
            ->count();

        // Get unread messages with sender details
        $unreadMessages = $user->emailsReceived()
            ->whereNull('read_at')
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'messages_page');

        return [
            'unreadMessagesCount' => $unreadMessagesCount,
            'unreadMessages' => $unreadMessages
        ];
    }

    /**
     * Get listing data for the dashboard (customer role).
     *
     * @param \App\Models\User|null $user
     * @return array
     */
    private function getListingData($user)
    {
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
                    ->orderBy('created_at', 'desc')
                    ->paginate(5, ['*'], 'listings_page');
            }
        }

        return [
            'openListingsCount' => $openListingsCount,
            'openListings' => $openListings
        ];
    }

    /**
     * Get quote data for the dashboard (specialist role).
     *
     * @param \App\Models\User|null $user
     * @return array
     */
    private function getQuoteData($user)
    {
        $openQuotes = collect();

        if ($user->hasRole('specialist')) {
            // Get open quotes with details
            $openStatusId = QuoteStatus::where('name', 'Open')->first()->id ?? null;
            if ($openStatusId) {
                $openQuotes = Quote::where('user_id', $user->id)
                    ->where('status_id', $openStatusId)
                    ->with('listing')
                    ->orderBy('created_at', 'desc')
                    ->paginate(5, ['*'], 'quotes_page');
            }
        }

        return [
            'openQuotes' => $openQuotes
        ];
    }

    /**
     * Get order data for the dashboard (both roles).
     *
     * @param \App\Models\User|null $user
     * @return array
     */
    private function getOrderData($user)
    {
        $closedStatusId = OrderStatus::where('name', 'Closed')->first()->id ?? null;

        // Get customer orders data
        $customerOrdersByStatus = collect();
        $activeCustomerOrders = collect();

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

        $activeCustomerOrders = Order::where('customer_id', $user->id)
            ->when($closedStatusId, function ($query) use ($closedStatusId) {
                return $query->where('status_id', '!=', $closedStatusId);
            })
            ->with(['listing', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'customer_orders_page');

        // Get specialist orders data
        $specialistOrdersByStatus = collect();
        $activeSpecialistOrders = collect();

        if ($user->hasRole('specialist')) {
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

            $activeSpecialistOrders = Order::where('specialist_id', $user->id)
                ->when($closedStatusId, function ($query) use ($closedStatusId) {
                    return $query->where('status_id', '!=', $closedStatusId);
                })
                ->with(['listing', 'status'])
                ->orderBy('created_at', 'desc')
                ->paginate(5, ['*'], 'specialist_orders_page');
        }

        return [
            'customerOrdersByStatus' => $customerOrdersByStatus,
            'activeCustomerOrders' => $activeCustomerOrders,
            'specialistOrdersByStatus' => $specialistOrdersByStatus,
            'activeSpecialistOrders' => $activeSpecialistOrders
        ];
    }

    /**
     * Get activity summary for the user (AJAX endpoint).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activitySummary(Request $request)
    {
        $user = auth()->user();
        $days = $request->input('days', 7);
        $startDate = now()->subDays($days);
        $response = [];

        if ($user->hasRole('customer')) {
            $response['customer'] = [
                'listings_published' => Listing::where('user_id', $user->id)
                    ->where('published_at', '>=', $startDate)
                    ->count(),

                'orders_closed' => Order::where('customer_id', $user->id)
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'Closed');
                    })
                    ->where('updated_at', '>=', $startDate)
                    ->count()
            ];
        }

        if ($user->hasRole('specialist')) {
            $response['specialist'] = [
                'quotes_submitted' => Quote::where('user_id', $user->id)
                    ->where('created_at', '>=', $startDate)
                    ->count(),

                'orders_closed' => Order::where('specialist_id', $user->id)
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'Closed');
                    })
                    ->where('updated_at', '>=', $startDate)
                    ->count()
            ];
        }

        return response()->json($response);
    }
}