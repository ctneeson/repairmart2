<x-app-layout title="Dashboard" bodyClass="page-dashboard">
    <main>
        <div class="container py-4">
            <h1 class="mb-4">Welcome, {{ auth()->user()->name }}</h1>
            
            <div class="row row-eq-height">
                <!-- Quick Actions Row -->
                <div class="row mb-4 mb-medium">
                    <div class="col-md-6 mb-md-0">
                        <div class="card h-100">
                            <div class="card-header" style="display: flex !important; justify-content: space-between !important; flex-wrap: nowrap !important;">
                                <h5 class="card-title mb-0">Activity Summary</h5>
                                <div style="align-self: flex-end !important;">
                                    <select name="activity_period" id="activity_period"
                                        class="form-select form-select-sm"
                                        style="width: auto !important; min-width: 120px !important;
                                            margin: 0 !important;">
                                        <option value="7">Last 7 days</option>
                                        <option value="30">Last 30 days</option>
                                        <option value="90">Last 90 days</option>
                                    </select>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <!-- Tab Navigation -->
                                        <ul class="nav nav-tabs" id="activityTabs" role="tablist">
                                            @if(auth()->user()->hasRole('customer'))
                                            <li class="nav-item">
                                                <a class="nav-link active" id="customer-tab" data-bs-toggle="tab" 
                                                    data-bs-target="#customer-data" href="#customer-data" role="tab" 
                                                    aria-controls="customer-data" aria-selected="true">
                                                    Customer
                                                </a>
                                            </li>
                                            @endif
                                            
                                            @if(auth()->user()->hasRole('specialist'))
                                            <li class="nav-item">
                                                <a class="nav-link {{ !auth()->user()->hasRole('customer') ? 'active' : '' }}" 
                                                    id="specialist-tab" data-bs-toggle="tab"
                                                    data-bs-target="#specialist-data" href="#specialist-data" role="tab" 
                                                    aria-controls="specialist-data" 
                                                    aria-selected="{{ !auth()->user()->hasRole('customer') ? 'true' : 'false' }}">
                                                    Specialist
                                                </a>
                                            </li>
                                            @endif
                                        </ul>

                                        <!-- Tab Content -->
                                        <div class="tab-content pt-3" id="activityTabsContent">
                                            @if(auth()->user()->hasRole('customer'))
                                            <div class="tab-pane fade show active" id="customer-data" role="tabpanel" aria-labelledby="customer-tab">
                                                <table class="dashboard-activity-table-small" id="customer-activity-table">
                                                    <tbody>
                                                        <tr>
                                                            <th>Listings published:</th>
                                                            <td id="customer-listings-count" data-counts='{{ json_encode($activityData["customer"]["listings_published"]) }}'>
                                                                {{ $activityData["customer"]["listings_published"]["7"] }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Orders closed:</th>
                                                            <td id="customer-orders-count" data-counts='{{ json_encode($activityData["customer"]["orders_closed"]) }}'>
                                                                {{ $activityData["customer"]["orders_closed"]["7"] }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                            
                                            @if(auth()->user()->hasRole('specialist'))
                                            <div class="tab-pane fade {{ !auth()->user()->hasRole('customer') ? 'show active' : '' }}" 
                                                id="specialist-data" role="tabpanel" aria-labelledby="specialist-tab">
                                                <table class="dashboard-activity-table-small" id="specialist-activity-table">
                                                    <tbody>
                                                        <tr>
                                                            <th>Quotes submitted:</th>
                                                            <td id="specialist-quotes-count" data-counts='{{ json_encode($activityData["specialist"]["quotes_submitted"]) }}'>
                                                                {{ $activityData["specialist"]["quotes_submitted"]["7"] }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Orders closed:</th>
                                                            <td id="specialist-orders-count" data-counts='{{ json_encode($activityData["specialist"]["orders_closed"]) }}'>
                                                                {{ $activityData["specialist"]["orders_closed"]["7"] }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-md-0">
                    {{-- <div class="col-md-12" style="width: 100%; padding-left: 15px; padding-right: 15px;"> --}}
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body mb-small">
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('watchlist.index') }}"
                                        class="btn btn-add-new-listing">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="#000000"
                                            stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg> 
                                        My Watchlist
                                    </a>
                                    <a href="{{ route('listings.search') }}"
                                        class="btn btn-add-new-listing">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="#000000"
                                            stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        Search Listings
                                    </a>
                                    <a href="{{ route('profile.index') }}"
                                        class="btn btn-add-new-listing">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="#000000"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                        Update Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- First Row -->
                <div class="row mb-4 mb-medium">
                    <!-- Messages Panel -->
                    <div class="col-md-6 mb-md-0">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">Messages</h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <h4 class="mb-0" style="margin-top: 0.7rem; margin-bottom: 0.3rem;">
                                            {{ $unreadMessagesCount }} Unread {{ Str::plural('message', $unreadMessagesCount) }}
                                        </h4>
                                    </div>
                                </div>
                                
                                <div class="flex-grow-1 overflow-auto">
                                    @if($unreadMessages->count() > 0)
                                        <div class="table-responsive" style="max-height: 250px;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Subject</th>
                                                        <th>Sender</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($unreadMessages as $message)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('email.show', $message->id) }}">
                                                                    {{ Str::limit($message->subject, 30) }}
                                                                </a>
                                                            </td>
                                                            <td>{{ $message->sender->name }}</td>
                                                            <td>{{ $message->created_at->format('M d, Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-center mt-2">
                                            {{ $unreadMessages->links() }}
                                        </div>
                                    @else
                                        <p class="text-center">No unread messages.</p>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer mt-auto">
                                <a href="{{ route('email.index') }}"
                                    class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-envelope-fill"></i>
                                    View All Messages
                                </a>
                            </div>
                        </div>
                    </div>
                
                    <!-- Listings Panel (Customers only) -->
                    <div class="col-md-6 mb-md-0">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">Listings</h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                @if(auth()->user()->hasRole('customer'))
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h4 class="mb-0" style="margin-top: 0.7rem; margin-bottom: 0.3rem;">
                                                    {{ $openListingsCount }} Open {{ Str::plural('Listing', $openListingsCount) }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-auto">
                                        @if($openListings->count() > 0)
                                            <div class="table-responsive" style="max-height: 200px;">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Title</th>
                                                            <th>Expires</th>
                                                            <th>Quotes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($openListings as $listing)
                                                            <tr>
                                                                <td>
                                                                    <a href="{{ route('listings.show', $listing->id) }}">
                                                                        {{ Str::limit($listing->title, 30) }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $listing->published_at->addDays($listing->expiry_days)->format('M d, Y') }}</td>
                                                                <td>
                                                                    <span class="badge bg-info rounded-pill">
                                                                        {{ $listing->quotes_count }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex justify-content-center mt-2">
                                                {{ $openListings->links() }}
                                            </div>
                                        @else
                                            <p class="text-center">No open listings.</p>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-center">This feature is available to customers only.</p>
                                @endif
                            </div>
                            <div class="card-footer mt-auto">
                                <a href="{{ route('listings.index') }}"
                                    class="btn btn-sm btn-outline-success w-100">
                                    <i class="bi bi-person-lines-fill"></i>
                                    View My Listings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Second Row -->
                <div class="row mb-4">
                    <!-- Quotes Panel (Specialists only) -->
                    <div class="col-md-6 mb-md-0">
                        <div class="card h-100 mn-medium">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">Quotes</h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                @if(auth()->user()->hasRole('specialist'))
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h4 class="mb-0" style="margin-top: 0.7rem; margin-bottom: 0.3rem;">
                                                    {{ $openQuotes->count() }} Open {{ Str::plural('Quote', $openQuotes->count()) }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-grow-1 overflow-auto">
                                        @if($openQuotes->count() > 0)
                                            <div class="table-responsive" style="max-height: 200px;">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Listing Title</th>
                                                            <th>Expires</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($openQuotes as $quote)
                                                            <tr>
                                                                <td>
                                                                    <a href="{{ route('quotes.show', $quote->id) }}">
                                                                        {{ Str::limit($quote->listing->title, 30) }}
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    @if($quote->listing && $quote->listing->expiry_date)
                                                                        {{ $quote->listing->expiry_date->format('M d, Y') }}
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex justify-content-center mt-2">
                                                {{ $openQuotes->links() }}
                                            </div>
                                        @else
                                            <p class="text-center">No open quotes.</p>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-center">This feature is available to specialists only.</p>
                                @endif
                            </div>
                            <div class="card-footer mt-auto">
                                <a href="{{ route('quotes.index') }}"
                                    class="btn btn-sm btn-outline-info w-100">
                                    <i class="bi bi-tools"></i>
                                    View My Quotes
                                </a>
                            </div>
                        </div>
                    </div>
                
                    <!-- Orders Panel -->
                    <div class="col-md-6 mb-md-0">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Orders</h5>
                                <ul class="nav nav-tabs card-header-tabs" id="ordersTabGroup" style="margin-left: 1rem; margin-bottom: -0.5rem;">
                                    @if(auth()->user()->hasRole('customer'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ auth()->user()->hasRole('customer') ? 'active' : '' }}"
                                            id="customer-orders-tab"
                                            href="#customer-orders"
                                            data-bs-toggle="tab"
                                            data-bs-target="#customer-orders"
                                            role="tab"
                                            aria-controls="customer-orders"
                                            aria-selected="{{ auth()->user()->hasRole('customer') ? 'true' : 'false' }}">
                                            Customer Orders
                                        </a>
                                    </li>
                                    @endif
                                    
                                    @if(auth()->user()->hasRole('specialist'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ auth()->user()->hasRole('specialist') && !auth()->user()->hasRole('customer') ? 'active' : '' }}"
                                            id="specialist-orders-tab"
                                            href="#specialist-orders"
                                            data-bs-toggle="tab"
                                            data-bs-target="#specialist-orders"
                                            role="tab"
                                            aria-controls="specialist-orders"
                                            aria-selected="{{ auth()->user()->hasRole('specialist') && !auth()->user()->hasRole('customer') ? 'true' : 'false' }}">
                                            Specialist Orders
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                    
                            <div class="tab-content p-medium" id="ordersTabContent" style="flex-grow: 1;">
                                <!-- Customer Orders Tab -->
                                @if(auth()->user()->hasRole('customer'))
                                <div class="tab-pane {{ auth()->user()->hasRole('customer') ? 'show active' : 'fade' }}"
                                    id="customer-orders"
                                    style="{{ auth()->user()->hasRole('customer') ? '' : 'display: none;' }}">
                                    @if($activeCustomerOrders->count() > 0)
                                        <div class="table-responsive" style="max-height: 250px;">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Listing Title</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($activeCustomerOrders as $order)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('orders.show', $order->id) }}">
                                                                    {{ Str::limit($order->listing->title, 30) }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $order->status->name == 'Pending' ? 'secondary' : 'primary' }}">
                                                                    {{ $order->status->name }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-large">
                                            <p>No active orders found.</p>
                                        </div>
                                    @endif
                                </div>
                                @endif
                                
                                <!-- Specialist Orders Tab -->
                                @if(auth()->user()->hasRole('specialist'))
                                <div class="tab-pane {{ auth()->user()->hasRole('specialist') && !auth()->user()->hasRole('customer') ? 'show active' : 'fade' }}"
                                    id="specialist-orders"
                                    style="{{ auth()->user()->hasRole('specialist') && !auth()->user()->hasRole('customer') ? '' : 'display: none;' }}">
                                    @if($activeSpecialistOrders->count() > 0)
                                        <div class="table-responsive" style="max-height: 250px;">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Listing Title</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($activeSpecialistOrders as $order)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('orders.show', $order->id) }}">
                                                                    {{ Str::limit($order->listing->title, 30) }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $order->status->name == 'Pending' ? 'secondary' : 'primary' }}">
                                                                    {{ $order->status->name }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-large">
                                            <p>No active specialist orders.</p>
                                        </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                    
                            <div class="card-footer">
                                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-receipt-cutoff"></i>
                                    View All Orders
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @vite(['resources/js/dashboard-statistics.js'])

    </main>
</x-app-layout>