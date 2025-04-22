<x-app-layout title="Dashboard">
    <div class="container py-4">
        <h1 class="mb-4">Welcome, {{ auth()->user()->name }}</h1>
        
        <div class="row row-eq-height">
            <!-- Quick Actions Row -->
            <div class="row mb-4">
                <div class="col-md-12" style="width: 100%; padding-left: 15px; padding-right: 15px;">
                    <div class="card h-100">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('watchlist.index') }}"
                                        class="btn btn-add-new-listing">
                                        <i class="bi bi-eye-fill"></i> 
                                        My Watchlist
                                    </a>
                                    <a href="{{ route('listings.search') }}"
                                        class="btn btn-add-new-listing">
                                        <i class="bi bi-search"></i>
                                        Browse Repair Requests
                                    </a>
                                    <a href="{{ route('profile.index') }}"
                                        class="btn btn-add-new-listing">
                                        <i class="bi bi-person"></i>
                                        Update Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- First Row -->
            <div class="row mb-4">
                <!-- Messages Panel -->
                <div class="col-md-6 mb-md-0">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Messages</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h3 class="mb-0">
                                        {{ $unreadMessagesCount }} Unread {{ Str::plural('message', $unreadMessagesCount) }}
                                    </h3>
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
                                            <h3 class="mb-0">
                                                {{ $openListingsCount }} Open {{ Str::plural('Listing', $openListingsCount) }}
                                            </h3>
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
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">Quotes</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            @if(auth()->user()->hasRole('specialist'))
                                <div class="mb-3">
                                    @if($quotesByStatus->isEmpty())
                                        <p class="text-center">No quotes submitted yet.</p>
                                    @else
                                        <ul class="list-group list-group-flush">
                                            @foreach($quotesByStatus as $quote)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $quote['status_name'] }}
                                                <span class="badge bg-info rounded-pill">{{ $quote['count'] }}</span>
                                            </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                
                                <h6 class="border-bottom pb-2 mb-3">Open Quotes</h6>
                                
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
                        <div class="card-header bg-warning">
                            <h5 class="card-title mb-0">Orders</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="row g-0 flex-grow-1">
                                <!-- Customer Orders -->
                                <div class="col-12 mb-4">
                                    <h6 class="border-bottom pb-2 mb-3">As Customer</h6>
                                    
                                    <div class="mb-3">
                                        @if($customerOrdersByStatus->isEmpty())
                                            <p class="text-center">No customer orders yet.</p>
                                        @else
                                            <ul class="list-group list-group-flush small">
                                                @foreach($customerOrdersByStatus as $order)
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-1">
                                                    {{ $order['status_name'] }}
                                                    <span class="badge bg-warning text-dark rounded-pill">{{ $order['count'] }}</span>
                                                </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                    
                                    @if($activeCustomerOrders->count() > 0)
                                        <div class="table-responsive" style="max-height: 120px;">
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
                                        <div class="d-flex justify-content-center mt-2">
                                            {{ $activeCustomerOrders->links() }}
                                        </div>
                                    @else
                                        <p class="text-center">No active customer orders.</p>
                                    @endif
                                </div>
                                
                                <!-- Specialist Orders -->
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3">As Specialist</h6>
                                    
                                    <div class="mb-3">
                                        @if($specialistOrdersByStatus->isEmpty())
                                            <p class="text-center">No specialist orders yet.</p>
                                        @else
                                            <ul class="list-group list-group-flush small">
                                                @foreach($specialistOrdersByStatus as $order)
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-1">
                                                    {{ $order['status_name'] }}
                                                    <span class="badge bg-warning text-dark rounded-pill">{{ $order['count'] }}</span>
                                                </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                    
                                    @if($activeSpecialistOrders->count() > 0)
                                        <div class="table-responsive" style="max-height: 120px;">
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
                                        <div class="d-flex justify-content-center mt-2">
                                            {{ $activeSpecialistOrders->links() }}
                                        </div>
                                    @else
                                        <p class="text-center">No active specialist orders.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer mt-auto">
                            <a href="{{ route('orders.index') }}"
                                class="btn btn-sm btn-outline-warning w-100">
                                <i class="bi bi-receipt-cutoff"></i>
                                View All Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @media (min-width: 768px) {
            .col-md-6 {
                width: 50% !important;
                flex: 0 0 50% !important;
                max-width: 50% !important;
                padding-left: 15px;
                padding-right: 15px;
            }

            .row {
                margin-left: -15px;
                margin-right: -15px;
            }
        }

        .card.h-100 {
            height: 100% !important;
        }

        .row-eq-height {
            display: flex;
            flex-wrap: wrap;
        }
        
        .row-eq-height > [class*='col-'] {
            display: flex;
            flex-direction: column;
        }
        
        .row-eq-height .card {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .row {
            width: 100%;
        }
        
        .card-body {
            flex: 1 0 auto;
        }
        
        .flex-grow-1 {
            flex-grow: 1;
        }
        
        /* Rest of your original styles */
        .icon-circle {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .icon-circle i {
            font-size: 1.5rem;
        }
        
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .card-header {
            border-radius: calc(0.5rem - 1px) calc(0.5rem - 1px) 0 0;
            padding: 0rem 1.5rem;
        }
        
        .card-body {
            padding: 0rem 1.5rem;
        }
        
        .card-title {
            font-size: 20px;
            font-weight: bolder;
            margin-bottom: 0.5rem;
        }
        
        .card-footer {
            padding: 1rem 1.5rem;
            background-color: rgba(0, 0, 0, 0.03);
        }
        
        .table th {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .table td {
            font-size: 0.9rem;
        }
        
        .table-sm th, .table-sm td {
            padding: 0.4rem;
        }
        
        /* Handle pagination style */
        .pagination {
            justify-content: center;
            font-size: 0.9rem;
        }
        
        .pagination .page-item .page-link {
            padding: 0.25rem 0.5rem;
        }
    </style>
</x-app-layout>