<x-app-layout title="Orders" bodyClass="page-orders-index">
    <main>
        <div class="container-small">
            <div class="d-flex justify-content-between align-items-center mb-medium">
                <h1 class="page-title">Orders</h1>
            </div>

            <div class="card">
                <div class="card-header"
                    style="background-color: white; border-bottom: 1px solid #e2e8f0; padding: 0;">
                    <ul class="nav nav-tabs card-header-tabs" style="margin-left: 1rem;">
                        @if(auth()->user()->hasRole('customer'))
                        <li class="nav-item">
                            <a class="nav-link {{ !auth()->user()->hasRole('specialist') ? 'active' : '' }}"
                                href="#customer-orders">
                                My Orders
                                @if($customerOrders->where('status_id', 1)->count() > 0)
                                    <span class="badge rounded-pill"
                                        style="color: white; background-color: #3490dc;
                                            margin-left: 5px;">
                                        ({{ $customerOrders->where('status_id', 1)->count() }})
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->hasRole('specialist'))
                        <li class="nav-item">
                            <a class="nav-link {{ !auth()->user()->hasRole('customer')
                                || (auth()->user()->hasRole('customer')
                                && auth()->user()->hasRole('specialist')) ? 'active' : '' }}"
                                href="#specialist-orders">
                                Repair Orders
                                @if($specialistOrders->where('status_id', 1)->count() > 0)
                                    <span class="badge rounded-pill"
                                        style="color: white; background-color: #38a169; margin-left: 5px;">
                                        ({{ $specialistOrders->where('status_id', 1)->count() }})
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>

                <div class="tab-content p-medium">
                    <!-- Customer Orders Tab -->
                    @if(auth()->user()->hasRole('customer'))
                    <div class="tab-pane {{ !auth()->user()->hasRole('specialist')
                            ? 'show active' : 'fade' }}"
                        id="customer-orders"
                        style="{{ !auth()->user()->hasRole('specialist')
                            ? '' : 'display: none;' }}">
                        @if($customerOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #e2e8f0;">
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Listing</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Specialist</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Created</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Status</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Amount</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customerOrders as $order)
                                            <tr style="border-bottom: 1px solid #e2e8f0; 
                                                {{ $order->status_id === 1
                                                    ? 'font-weight: bold; background-color: #f8fafc;' : '' }}">
                                                <td data-label="Listing"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <a href="{{ route('listings.show', $order->quote->listing->id) }}"
                                                        class="text-decoration-none">
                                                        {{ $order->quote->listing->title }}
                                                    </a>
                                                </td>
                                                <td data-label="Specialist"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <a href="{{ route('profile.show', $order->repairSpecialist) }}"
                                                        class="text-decoration-none">
                                                        {{ $order->repairSpecialist->name }}
                                                    </a>
                                                </td>
                                                <td data-label="Created"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $order->created_at->format('M j, Y') }}
                                                </td>
                                                <td data-label="Status"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <span class="badge bg-{{ $order->status->color }}">
                                                        {{ $order->status->name }}
                                                    </span>
                                                </td>
                                                <td data-label="Amount"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $order->currency->symbol }} {{ number_format($order->amount, 2) }}
                                                </td>
                                                <td data-label="Actions" 
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                                                    <div class="order-action-buttons">
                                                        <a href="{{ route('orders.show', $order->id) }}"
                                                            class="btn btn-sm btn-outline-primary btn-block text-center">
                                                            View
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-medium">
                                {{ $customerOrders->links() }}
                            </div>
                        @else
                            <div class="text-center py-large">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1" stroke="#718096" style="width: 64px; height: 64px; margin: 0 auto 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5
                                            7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621
                                            0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0
                                            1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <h3>No orders found</h3>
                                <p class="text-muted">You don't have any orders yet.</p>
                            </div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Specialist Orders Tab -->
                    @if(auth()->user()->hasRole('specialist'))
                    <div class="tab-pane {{ !auth()->user()->hasRole('customer')
                            || (auth()->user()->hasRole('customer')
                            && auth()->user()->hasRole('specialist'))
                            ? 'show active' : 'fade' }}"
                        id="specialist-orders"
                        style="{{ !auth()->user()->hasRole('customer')
                            || (auth()->user()->hasRole('customer')
                            && auth()->user()->hasRole('specialist'))
                            ? '' : 'display: none;' }}">
                        @if($specialistOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #e2e8f0;">
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Listing</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Customer</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Created</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Status</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Amount</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($specialistOrders as $order)
                                            <tr style="border-bottom: 1px solid #e2e8f0; 
                                                {{ $order->status_id === 1
                                                    ? 'font-weight: bold; background-color: #f8fafc;' : '' }}">
                                                <td data-label="Listing"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <a href="{{ route('listings.show', $order->quote->listing->id) }}"
                                                        class="text-decoration-none">
                                                        {{ $order->quote->listing->title }}
                                                    </a>
                                                </td>
                                                <td data-label="Customer"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <a href="{{ route('profile.show', $order->customer) }}"
                                                        class="text-decoration-none">
                                                        {{ $order->customer->name }}
                                                    </a>
                                                </td>
                                                <td data-label="Created"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $order->created_at->format('M j, Y') }}
                                                </td>
                                                <td data-label="Status"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <span class="badge bg-{{ $order->status->color }}">
                                                        {{ $order->status->name }}
                                                    </span>
                                                </td>
                                                <td data-label="Amount"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $order->currency->symbol }} {{ number_format($order->amount, 2) }}
                                                </td>
                                                <td data-label="Actions" 
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                                                    <div class="order-action-buttons">
                                                        <a href="{{ route('orders.show', $order->id) }}"
                                                            class="btn btn-sm btn-outline-primary btn-block text-center">
                                                            View
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-medium">
                                {{ $specialistOrders->links() }}
                            </div>
                        @else
                            <div class="text-center py-large">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1" stroke="#718096" 
                                    style="width: 64px; height: 64px; margin: 0 auto 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125
                                            1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75
                                            12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504
                                            1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0
                                            00-9-9z" />
                                </svg>
                                <h3>No repair orders found</h3>
                                <p class="text-muted">You don't have any repair orders assigned to you.</p>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all elements
            const tabs = document.querySelectorAll('.nav-link');
            const tabContents = document.querySelectorAll('.tab-pane');
            
            // On initial load, hide any non-active tab content
            tabContents.forEach(content => {
                if (!content.classList.contains('active')) {
                    content.style.display = 'none';
                } else {
                    content.style.display = 'block';
                }
            });
            
            // Add click handlers to each tab
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Get the target tab ID from the href attribute
                    const targetId = this.getAttribute('href').substring(1);
                    showTab(targetId);
                });
            });
            
            function showTab(tabId) {
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.style.display = 'none';
                    content.classList.remove('show', 'active');
                });
                
                // Remove active class from all tabs
                tabs.forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // Show the selected tab content
                const selectedTab = document.getElementById(tabId);
                if (selectedTab) {
                    selectedTab.style.display = 'block';
                    selectedTab.classList.add('show', 'active');
                    
                    // Add active class to the selected tab
                    const activeTabLink = document.querySelector(`.nav-link[href="#${tabId}"]`);
                    if (activeTabLink) {
                        activeTabLink.classList.add('active');
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>