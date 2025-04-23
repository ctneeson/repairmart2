<x-app-layout title="Quotes" bodyClass="page-quotes-index">
    <main>
        <div class="container">
            <h1 class="quote-details-page-title">Quotes</h1>

            <div class="card p-medium">
                <div class="card-header">
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
                                        <tr>
                                            <th>Image</th>
                                            <th>Listing</th>
                                            <th>Expiry</th>
                                            <th>Status</th>
                                            <th>Delivery Method</th>
                                            <th style="min-width: 120px; width: 120px;">Amount</th>
                                            <th>Updated</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($receivedQuotes as $quote)
                                            <tr>
                                                <td>
                                                    @php
                                                      $attachmentUrl = $quote->listing->primaryAttachment?->getUrl() ?: '/img/no-photo-available.jpg';
                                                      $filePath = $quote->listing->primaryAttachment ? Storage::disk('public')->path($quote->listing->primaryAttachment->path) : public_path($attachmentUrl);
                                                      $mimeType = $quote->listing->primaryAttachment ? mime_content_type($filePath) : 'image/jpeg';
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
                                                    <a href="{{ route('listings.show', $quote->listing->id) }}"
                                                        class="text-decoration-none">
                                                        {{ $quote->listing->title }}
                                                    </a>
                                                </td>
                                                <td data-label="Expiry">
                                                    {{ $quote->listing->getExpiryDateAttribute()->format('Y-m-d') }}
                                                </td>
                                                <td data-label="Status">
                                                    <span class="badge
                                                        quote-status-{{ strtolower(str_replace(' ', '-', $quote->status->name)) }}">
                                                        {{ $quote->status->name }}
                                                    </span>
                                                </td>
                                                <td data-label="Delivery Method">
                                                    {{ $quote->deliveryMethod->name }}
                                                </td>
                                                <td data-label="Amount">
                                                    {{ $quote->currency->iso_code }} {{ number_format($quote->amount, 2) }}
                                                </td>
                                                <td data-label="Updated">
                                                    {{ $quote->getUpdatedDate() }}
                                                </td>
                                                <td data-label="Actions" class="actions-cell">
                                                    <div class="action-buttons-container">
                                                        <a href="{{ route('quotes.show', $quote->id) }}"
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
                                {{ $receivedQuotes->links() }}
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
                                        <tr>
                                            <th>Image</th>
                                            <th>Listing</th>
                                            <th>Published</th>
                                            <th>Status</th>
                                            <th>Delivery Method</th>
                                            <th style="min-width: 120px; width: 120px;">Amount</th>
                                            <th>Updated</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($submittedQuotes as $quote)
                                            <tr>
                                                <td>
                                                    @php
                                                      $attachmentUrl = $quote->listing->primaryAttachment?->getUrl() ?: '/img/no-photo-available.jpg';
                                                      $filePath = $quote->listing->primaryAttachment ? Storage::disk('public')->path($quote->listing->primaryAttachment->path) : public_path($attachmentUrl);
                                                      $mimeType = $quote->listing->primaryAttachment ? mime_content_type($filePath) : 'image/jpeg';
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
                                                    <a href="{{ route('listings.show', $quote->listing->id) }}"
                                                        class="text-decoration-none">
                                                        {{ $quote->listing->title }}
                                                    </a>
                                                </td>
                                                <td data-label="Created">
                                                    {{ $quote->listing->getPublishedDate() }}
                                                </td>
                                                <td data-label="Status">
                                                    <span class="badge quote-status-{{ strtolower(str_replace(' ', '-', $quote->status->name)) }}">
                                                        {{ $quote->status->name }}
                                                    </span>
                                                </td>
                                                <td data-label="Delivery Method">
                                                    {{ $quote->deliveryMethod->name }}
                                                </td>
                                                <td data-label="Amount">
                                                    {{ $quote->currency->iso_code }} {{ number_format($quote->amount, 2) }}
                                                </td>
                                                <td data-label="Updated">
                                                    {{ $quote->getUpdatedDate() }}
                                                </td>
                                                <td data-label="Actions" class="actions-cell">
                                                    <div class="action-buttons-container">
                                                        <a href="{{ route('quotes.show', $quote->id) }}"
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
                                                        @if($quote->status->name === 'Open')
                                                            <a href="{{ route('quotes.edit', $quote->id) }}"
                                                                class="btn btn-edit">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                <path stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                                </svg>
                                                                Edit
                                                            </a>
                                                            <form action="{{ route('quotes.destroy', $quote->id) }}" 
                                                                  method="POST" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this quote?')"
                                                                  class="d-block">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-delete">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                        stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                                            <line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line>
                                                                        </svg>
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
                                    stroke-width="1" stroke="#718096">
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