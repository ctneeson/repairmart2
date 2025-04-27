<x-app-layout title="Quotes" bodyClass="page-quotes-index">
    <main>
        @if(app()->environment('local'))
            <div class="alert alert-debug mb-medium">
                <strong>Debug Info:</strong>
                <ul>
                    <li>Active Tab: {{ $activeTab }}</li>
                    <li>User Roles: {{ implode(', ', auth()->user()->roles->pluck('name')->toArray()) }}</li>
                    <li>Filter Listing ID: {{ request('listing_id') }}</li>
                    <li>Filter Listing Object: {{ isset($filterListing) ? 'Yes (ID: '.$filterListing->id.')' : 'No' }}</li>
                </ul>
            </div>
        @endif
        <div class="container">
            <h1 class="quote-details-page-title">Quotes</h1>

            @if(isset($filterListing))
                <div class="alert alert-info mb-medium">
                    <div class="d-flex">
                        <div>
                            <span class="badge bg-default" style="font-size: 1rem">
                                <strong>
                                    Showing quotes for listing #{{ $filterListing->id }}:
                                </strong> {{ $filterListing->title }}
                            </span>
                            <a href="{{ route('quotes.index') }}"
                                class="btn btn-sm btn-outline-primary">
                                Clear filter
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($filterListing))
                <div class="alert alert-info mb-medium">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Showing quotes for listing #{{ $filterListing->id }}:</strong> 
                            {{ $filterListing->title }}
                        </div>
                        <a href="{{ route('quotes.index', ['tab' => $activeTab]) }}" 
                        class="btn btn-sm btn-outline-primary">
                            Clear filter
                        </a>
                    </div>
                </div>
            @endif

            <div class="card p-medium">
                <div class="card-header">
                    <ul class="nav nav-tabs">
                        @if(auth()->user()->hasRole('customer'))
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'received' ? 'active' : '' }}" 
                                   href="{{ route('quotes.index', ['tab' => 'received', 'listing_id' => request('listing_id')]) }}">
                                    Quotes Received
                                    @if($receivedPendingCount > 0)
                                        <span class="badge bg-primary">{{ $receivedPendingCount }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        
                        @if(auth()->user()->hasRole('specialist'))
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'submitted' ? 'active' : '' }}" 
                                    href="{{ route('quotes.index', ['tab' => 'submitted', 'listing_id' => request('listing_id')]) }}">
                                    Quotes Submitted
                                    @if($submittedOpenCount > 0)
                                        <span class="badge bg-primary">{{ $submittedOpenCount }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="tab-content p-medium">
                    <!-- Quotes Received Tab -->
                    @if(auth()->user()->hasRole('customer'))
                    <div class="tab-pane {{ $activeTab === 'received' ? 'show active' : 'fade' }}"
                        id="received"
                        style="{{ $activeTab === 'received' ? '' : 'display: none;' }}">
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
                                {{ $receivedQuotes->appends(['tab' => $activeTab, 'listing_id' => request('listing_id')])->links() }}
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
                    <div class="tab-pane {{ $activeTab === 'submitted' ? 'show active' : 'fade' }}"
                        id="submitted"
                        style="{{ $activeTab === 'submitted' ? '' : 'display: none;' }}">
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
                                {{ $submittedQuotes->appends(['tab' => $activeTab, 'listing_id' => request('listing_id')])->links() }}
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
            // Just ensure initial visibility based on active class
            document.querySelectorAll('.tab-pane').forEach(content => {
                if (content.classList.contains('active')) {
                    content.style.display = 'block';
                } else {
                    content.style.display = 'none';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>