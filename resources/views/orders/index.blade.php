<x-app-layout title="Orders" bodyClass="page-orders-index">
    <main>
        <div class="container">
            <h1 class="order-details-page-title">Orders</h1>

            <div class="card p-medium">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" style="margin-left: 1rem;">
                        @if(auth()->user()->hasRole('customer'))
                        <li class="nav-item">
                            <a class="nav-link {{ auth()->user()->hasRole('customer') ? 'active' : '' }}"
                                href="#customer-orders">
                                Customer Orders
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
                            <a class="nav-link {{ auth()->user()->hasRole('specialist')
                                && !auth()->user()->hasRole('customer') ? 'active' : '' }}"
                                href="#specialist-orders">
                                Specialist Orders
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
                    <div class="tab-pane {{ auth()->user()->hasRole('customer') ? 'show active' : 'fade' }}"
                        id="customer-orders"
                        style="{{ auth()->user()->hasRole('customer') ? '' : 'display: none;' }}">
                        @if($customerOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Listing</th>
                                            <th>Specialist</th>
                                            <th>Created</th>
                                            <th>Status</th>
                                            <th style="min-width: 120px; width: 120px;">Amount</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customerOrders as $order)
                                            <tr>
                                                <td>
                                                    @php
                                                      $attachmentUrl = $order->listing->primaryAttachment?->getUrl() ?: '/img/no-photo-available.jpg';
                                                      $filePath = $order->listing->primaryAttachment ? Storage::disk('public')->path($order->listing->primaryAttachment->path) : public_path($attachmentUrl);
                                                      $mimeType = $order->listing->primaryAttachment ? mime_content_type($filePath) : 'image/jpeg';
                                                    @endphp
                                                    @if (str_starts_with($mimeType, 'image/'))
                                                      <img
                                                        src="{{ $attachmentUrl }}"
                                                        alt=""
                                                        class="my-listings-img-thumbnail"
                                                      />
                                                    @elseif (str_starts_with($mimeType, 'video/'))
                                                      <video
                                                        src="{{ $attachmentUrl }}"
                                                        class="my-listings-img-thumbnail"
                                                      ></video>
                                                    @else
                                                      <img
                                                        src="/img/no-photo-available.jpg"
                                                        alt=""
                                                        class="my-listings-img-thumbnail"
                                                      />
                                                    @endif
                                                </td>
                                                <td data-label="Listing">
                                                    <a href="{{ route('listings.show', $order->listing->id) }}"
                                                        class="text-decoration-none">
                                                        {{ $order->listing->title }}
                                                    </a>
                                                </td>
                                                <td data-label="Specialist">
                                                    <a href="{{ route('profile.show', $order->repairSpecialist) }}"
                                                        class="text-decoration-none">
                                                        {{ $order->repairSpecialist->name }}
                                                    </a>
                                                </td>
                                                <td data-label="Created">
                                                    {{ $order->created_at->format('Y-m-d') }}
                                                </td>
                                                <td data-label="Status">
                                                    <span class="badge
                                                        order-status-{{ strtolower(str_replace(' ', '-', $order->status->name)) }}">
                                                        {{ $order->status->name }}
                                                        @if($order->status->name === 'Closed' && $order->customer_feedback_id === null)
                                                            <small class="awaiting-feedback">: awaiting feedback</small>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td data-label="Amount">
                                                    {{ $order->currency->iso_code }} {{ number_format($order->amount, 2) }}
                                                </td>
                                                <td data-label="Actions" class="actions-cell">
                                                    <div class="action-buttons-container">
                                                        <a href="{{ route('orders.show', $order->id) }}"
                                                            class="btn btn-edit">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke-width="1.5" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z">
                                                                </path>
                                                                <circle cx="12" cy="12" r="3"></circle>
                                                            </svg>
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
                                    stroke-width="1" stroke="#718096">
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
                    <div class="tab-pane {{ auth()->user()->hasRole('specialist')
                        && !auth()->user()->hasRole('customer') ? 'show active' : 'fade' }}"
                        id="specialist-orders"
                        style="{{ auth()->user()->hasRole('specialist')
                            && !auth()->user()->hasRole('customer') ? '' : 'display: none;' }}">
                        @if($specialistOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Listing</th>
                                            <th>Customer</th>
                                            <th>Created</th>
                                            <th>Status</th>
                                            <th style="min-width: 120px; width: 120px;">Amount</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($specialistOrders as $order)
                                            <tr>
                                                <td>
                                                    @php
                                                      $attachmentUrl = $order->listing->primaryAttachment?->getUrl() ?: '/img/no-photo-available.jpg';
                                                      $filePath = $order->listing->primaryAttachment ? Storage::disk('public')->path($order->listing->primaryAttachment->path) : public_path($attachmentUrl);
                                                      $mimeType = $order->listing->primaryAttachment ? mime_content_type($filePath) : 'image/jpeg';
                                                    @endphp
                                                    @if (str_starts_with($mimeType, 'image/'))
                                                      <img
                                                        src="{{ $attachmentUrl }}"
                                                        alt=""
                                                        class="my-listings-img-thumbnail"
                                                      />
                                                    @elseif (str_starts_with($mimeType, 'video/'))
                                                      <video
                                                        src="{{ $attachmentUrl }}"
                                                        class="my-listings-img-thumbnail"
                                                      ></video>
                                                    @else
                                                      <img
                                                        src="/img/no-photo-available.jpg"
                                                        alt=""
                                                        class="my-listings-img-thumbnail"
                                                      />
                                                    @endif
                                                </td>
                                                <td data-label="Listing">
                                                    <a href="{{ route('listings.show', $order->listing->id) }}"
                                                        class="text-decoration-none">
                                                        {{ $order->listing->title }}
                                                    </a>
                                                </td>
                                                <td data-label="Customer">
                                                    <a href="{{ route('profile.show', $order->customer) }}"
                                                        class="text-decoration-none">
                                                        {{ $order->customer->name }}
                                                    </a>
                                                </td>
                                                <td data-label="Created">
                                                    {{ $order->created_at->format('Y-m-d') }}
                                                </td>
                                                <td data-label="Status">
                                                    <span class="badge
                                                        order-status-{{ strtolower(str_replace(' ', '-', $order->status->name)) }}">
                                                        {{ $order->status->name }}
                                                        @if($order->status->name === 'Closed' && $order->specialist_feedback_id === null)
                                                            <small class="awaiting-feedback">: awaiting feedback</small>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td data-label="Amount">
                                                    {{ $order->currency->iso_code }} {{ number_format($order->amount, 2) }}
                                                </td>
                                                <td data-label="Actions" class="actions-cell">
                                                    <div class="action-buttons-container">
                                                        <a href="{{ route('orders.show', $order->id) }}"
                                                            class="btn btn-edit">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke-width="1.5" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z">
                                                                </path>
                                                                <circle cx="12" cy="12" r="3"></circle>
                                                            </svg>
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
                                    stroke-width="1" stroke="#718096">
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