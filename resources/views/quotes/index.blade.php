<x-app-layout title="Quotes" bodyClass="page-quotes-index">
    <main>
        <div class="container-small">
            <div class="d-flex justify-content-between align-items-center mb-medium">
                <h1 class="page-title">Quotes</h1>
            </div>

            <div class="card">
                <div class="card-header"
                    style="background-color: white; border-bottom: 1px solid #e2e8f0; padding: 0;">
                    <ul class="nav nav-tabs card-header-tabs" style="margin-left: 1rem;">
                        @if(auth()->user()->hasRole('customer'))
                        <li class="nav-item">
                            <a class="nav-link {{ auth()->user()->hasRole('customer') ? 'active' : '' }}"
                               href="#received">
                                Quotes Received
                                @if($receivedPendingCount > 0)
                                    <span class="badge rounded-pill"
                                        style="color: white; background-color: #3490dc;
                                            margin-left: 5px;">
                                        ({{ $receivedPendingCount }})
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->hasRole('specialist'))
                        <li class="nav-item">
                            <a class="nav-link {{ auth()->user()->hasRole('specialist')
                                && !auth()->user()->hasRole('customer') ? 'active' : '' }}"
                               href="#submitted">
                                Quotes Submitted
                                @if($submittedOpenCount > 0)
                                    <span class="badge rounded-pill"
                                        style="color: white; background-color: #38a169; margin-left: 5px;">
                                        ({{ $submittedOpenCount }})
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>

                <div class="tab-content p-medium">
                    <!-- Quotes Received Tab -->
                    @if(auth()->user()->hasRole('customer'))
                    <div class="tab-pane {{ auth()->user()->hasRole('customer') ? 'show active' : 'fade' }}"
                        id="received"
                        style="{{ auth()->user()->hasRole('customer') ? '' : 'display: none;' }}">
                        @if($receivedQuotes->count() > 0)
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #e2e8f0;">
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Listing</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Created</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Status</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Delivery Method</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Amount</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Updated</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($receivedQuotes as $quote)
                                            <tr style="border-bottom: 1px solid #e2e8f0; 
                                                {{ $quote->status->name === 'Open'
                                                    ? 'font-weight: bold; background-color: #f8fafc;' : '' }}">
                                                <td data-label="Listing"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <a href="{{ route('listings.show', $quote->listing->id) }}"
                                                        class="text-decoration-none">
                                                        {{ $quote->listing->title }}
                                                    </a>
                                                </td>
                                                <td data-label="Created"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $quote->listing->getPublishedDate() }}
                                                </td>
                                                <td data-label="Status"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <span class="badge
                                                        quote-status-{{ strtolower(str_replace(' ', '-', $quote->status->name)) }}">
                                                        {{ $quote->status->name }}
                                                    </span>
                                                </td>
                                                <td data-label="Delivery Method"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $quote->deliveryMethod->name }}
                                                </td>
                                                <td data-label="Amount"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $quote->currency->iso_code }} {{ number_format($quote->amount, 2) }}
                                                </td>
                                                <td data-label="Updated"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $quote->getUpdatedDate() }}
                                                </td>
                                                <td data-label="Actions" style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                                                    <div class="quote-action-buttons">
                                                        <a href="{{ route('quotes.show', $quote->id) }}"
                                                            class="btn btn-sm btn-outline-primary btn-block mb-1 text-center">
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
                                {{ $receivedQuotes->links() }}
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
                                <h3>No quotes received</h3>
                                <p class="text-muted">You haven't received any quotes yet.</p>
                            </div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Quotes Submitted Tab -->
                    @if(auth()->user()->hasRole('specialist'))
                    <div class="tab-pane {{ auth()->user()->hasRole('specialist')
                        && !auth()->user()->hasRole('customer') ? 'show active' : 'fade' }}"
                        id="submitted"
                        style="{{ auth()->user()->hasRole('specialist')
                            && !auth()->user()->hasRole('customer') ? '' : 'display: none;' }}">
                        @if($submittedQuotes->count() > 0)
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #e2e8f0;">
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Listing</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Published</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Status</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Delivery Method</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Amount</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Updated</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($submittedQuotes as $quote)
                                            <tr style="border-bottom: 1px solid #e2e8f0; {{ $quote->status->name === 'Open' ? 'font-weight: bold; background-color: #f8fafc;' : '' }}">
                                                <td data-label="Listing"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <a href="{{ route('listings.show', $quote->listing->id) }}"
                                                        class="text-decoration-none">
                                                        {{ $quote->listing->title }}
                                                    </a>
                                                </td>
                                                <td data-label="Created"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $quote->listing->getPublishedDate() }}
                                                </td>
                                                <td data-label="Status"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <span class="badge quote-status-{{ strtolower(str_replace(' ', '-', $quote->status->name)) }}">
                                                        {{ $quote->status->name }}
                                                    </span>
                                                </td>
                                                <td data-label="Delivery Method"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $quote->deliveryMethod->name }}
                                                </td>
                                                <td data-label="Amount"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $quote->currency->iso_code }} {{ number_format($quote->amount, 2) }}
                                                </td>
                                                <td data-label="Updated"
                                                    style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    {{ $quote->getUpdatedDate() }}
                                                </td>
                                                <td data-label="Actions" style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                                                    <div class="quote-action-buttons">
                                                        <a href="{{ route('quotes.show', $quote->id) }}"
                                                            class="btn btn-sm btn-outline-primary btn-block mb-1 text-center">
                                                            View
                                                        </a>
                                                        @if($quote->status->name === 'Open')
                                                            <a href="{{ route('quotes.edit', $quote->id) }}"
                                                                class="btn btn-sm btn-outline-secondary btn-block mb-1 text-center">
                                                                Edit
                                                            </a>
                                                            <form action="{{ route('quotes.destroy', $quote->id) }}" 
                                                                  method="POST" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this quote?')"
                                                                  class="d-block">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger btn-block text-center">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-medium">
                                {{ $submittedQuotes->links() }}
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
                                <h3>No quotes submitted</h3>
                                <p class="text-muted">You haven't submitted any quotes yet.</p>
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