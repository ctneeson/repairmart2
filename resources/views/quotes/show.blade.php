<x-app-layout title="View Quote">
    <main>
        <div class="container-small">
            <!-- Status and Actions Bar -->
            <div class="quote-header" style="margin-bottom: 1rem !important; position: relative; display: block; clear: both;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <!-- Left side: Quote title -->
                    <h1 class="mb-0">Quote #{{ $quote->id }}</h1>
                    
                    <!-- Right side: Status badge with explicit styling -->
                    <div class="quote-status-badge status-{{ strtolower(str_replace(' ', '-', $quote->status->name)) }}" 
                        style="float: right; clear: both; position: relative; z-index: 10;">
                        {{ $quote->status->name }}
                    </div>
                </div>
                
                <!-- Clear both to ensure actions start below the status badge -->
                <div style="clear: both;"></div>
                
                <!-- Action buttons in their own row with more spacing -->
                <div class="quote-actions" style="margin-bottom: 1rem !important; margin-top: 1rem !important;">
                    @if(auth()->id() === $quote->customer->id && $quote->status_id === 1)
                        <button type="button" class="btn btn-success me-2"
                            onclick="alert('Accept functionality will be added later')">
                            Accept Quote
                        </button>
                    @endif

                    @if(auth()->id() === $quote->user_id && $quote->status_id === 1)
                        <a href="{{ route('quotes.edit', $quote->id) }}" class="btn btn-primary me-2">
                            Edit Quote
                        </a>
                    @endif
                    
                    @if(auth()->user()->roles->where('name', 'admin')->count() > 0)
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="adminActionsDropdown" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                Admin Actions
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="adminActionsDropdown">
                                <li><a class="dropdown-item" href="{{ route('quotes.edit', $quote->id) }}">
                                    Edit Quote
                                </a></li>
                                <li><a class="dropdown-item" href="#"
                                    onclick="alert('Accept functionality will be added later')">
                                    Accept Quote
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('quotes.destroy', $quote->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this quote?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            Delete Quote
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quote Details Card -->
            <div class="card p-large mb-large">
                <div class="row">
                    <!-- Left Column: Quote Information -->
                    <div class="col-md-8 pe-md-4">
                        <h2 class="mb-small">Quote Details</h2>
                        
                        <div class="quote-details">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label>Amount</label>
                                        <div class="detail-value">
                                            {{ $quote->currency->symbol ?? '' }} {{ number_format($quote->amount, 2) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label>Turnaround Time</label>
                                        <div class="detail-value">
                                            {{ $quote->turnaround }} {{ Str::plural('day', $quote->turnaround) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label>Delivery Method</label>
                                        <div class="detail-value">
                                            {{ $quote->deliveryMethod->name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label>Created On</label>
                                        <div class="detail-value">
                                            {{ $quote->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="detail-group">
                                        <label>Quote Details</label>
                                        <div class="detail-value quote-details-text">
                                            {!! nl2br(e($quote->details)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($quote->attachments->count() > 0)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h3 class="mb-small">Attachments</h3>
                                        <div class="attachment-grid">
                                            @foreach($quote->attachments as $attachment)
                                                <div class="attachment-item">
                                                    <a href="{{ Storage::url($attachment->path) }}"
                                                        target="_blank" class="attachment-link">
                                                        @if(Str::contains($attachment->mime_type, 'image'))
                                                            <img src="{{ Storage::url($attachment->path) }}"
                                                                alt="{{ $attachment->filename }}"
                                                                class="attachment-thumbnail">
                                                        @elseif(Str::contains($attachment->mime_type, 'video'))
                                                            <div class="video-thumbnail">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" fill="currentColor"
                                                                    class="bi bi-film" viewBox="0 0 16 16">
                                                                    <path d="M0 1a1 1 0 0 1 1-1h14a1 1 0 0 1 1
                                                                        1v14a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V1zm4
                                                                        0v6h8V1H4zm8 8H4v6h8V9zM1 1v2h2V1H1zm2
                                                                        3H1v2h2V4zM1 7v2h2V7H1zm2 3H1v2h2v-2zm-2
                                                                        3v2h2v-2H1zM15 1h-2v2h2V1zm-2 3v2h2V4h-2zm2
                                                                        3h-2v2h2V7zm-2 3v2h2v-2h-2zm2 3h-2v2h2v-2z"/>
                                                                </svg>
                                                            </div>
                                                        @elseif(Str::contains($attachment->mime_type, 'pdf'))
                                                            <div class="document-thumbnail">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    width="24" height="24" fill="currentColor"
                                                                    class="bi bi-file-pdf" viewBox="0 0 16 16">
                                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2
                                                                        2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5
                                                                        0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0
                                                                        1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                                                    <path d="M4.603 14.087a.81.81 0 0
                                                                        1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68
                                                                        7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0
                                                                        1.062-2.227 7.269 7.269 0 0
                                                                        1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7
                                                                        0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27
                                                                        1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1
                                                                        1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04
                                                                        1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712
                                                                        5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307
                                                                        0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0
                                                                        1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266
                                                                        0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0
                                                                        .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858
                                                                        20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107
                                                                        0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876
                                                                        3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613
                                                                        0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
                                                                </svg>
                                                            </div>
                                                        @else
                                                            <div class="document-thumbnail">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    width="24" height="24" fill="currentColor"
                                                                    class="bi bi-file-text" viewBox="0 0 16 16">
                                                                    <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5
                                                                        0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0
                                                                        0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0
                                                                        0 0-1H5z"/>
                                                                    <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2
                                                                        2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1
                                                                        1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                        <div class="attachment-name">{{ Str::limit($attachment->filename, 15) }}</div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Link to original listing -->
                        <div class="mt-4 original-listing-link">
                            <a href="{{ route('listings.show', $quote->listing->id) }}"
                                class="btn btn-outline-secondary btn-sm">
                                View Original Listing
                            </a>
                        </div>
                    </div>

                    <!-- Right Column: Contact Information -->
                    <div class="col-md-4">
                        <div class="contact-sidebar">
                            <!-- Repair Specialist Information -->
                            <div class="contact-details mb-4">
                                <h3>Specialist Information</h3>
                                <div class="contact-info">
                                    <div class="contact-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2
                                                2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6
                                                4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68
                                                10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83
                                                1.418-.832 1.664h10z"/>
                                        </svg>
                                        <span>{{ $quote->repairSpecialist->name }}</span>
                                    </div>
                                    @if($quote->use_default_location)
                                        <div class="contact-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                                                <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493
                                                    31.493 0 0 1 8 14.58a31.481 31.481 0 0
                                                    1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3
                                                    6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8
                                                    16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                                                <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3
                                                    3 0 0 0 0 6z"/>
                                            </svg>
                                            <span>{{ $quote->repairSpecialist->city }}, {{ $quote->repairSpecialist->country->name }}</span>
                                        </div>
                                    @endif
                                    @if($quote->phone)
                                        <div class="contact-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                                                <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605
                                                    2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168
                                                    6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033
                                                    1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678
                                                    0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745
                                                    1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654
                                                    1.328zM1.884.511a1.745 1.745 0 0 1
                                                    2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0
                                                    .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0
                                                    1 1.494.315l2.306 1.794c.829.645.905
                                                    1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634
                                                    18.634 0 0 1-7.01-4.42 18.634 18.634 0 0
                                                    1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                                            </svg>
                                            <span>{{ $quote->phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Address Information (if not using default) -->
                            @if(!$quote->use_default_location)
                                <div class="address-details mb-4">
                                    <h3>Address</h3>
                                    <address>
                                        {{ $quote->address_line1 }}<br>
                                        @if($quote->address_line2)
                                            {{ $quote->address_line2 }}<br>
                                        @endif
                                        {{ $quote->city }}, {{ $quote->postcode }}<br>
                                        {{ $quote->country->name }}
                                    </address>
                                </div>
                            @endif

                            <!-- Customer Information -->
                            <div class="customer-details">
                                <h3>Customer Information</h3>
                                <div class="contact-info">
                                    <div class="contact-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2
                                                2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6
                                                4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68
                                                10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83
                                                1.418-.832 1.664h10z"/>
                                        </svg>
                                        <span>{{ $quote->customer->name }}</span>
                                    </div>
                                    <div class="contact-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                                            <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493
                                                31.493 0 0 1 8 14.58a31.481 31.481 0 0
                                                1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3
                                                6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8
                                                16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3
                                                0 0 0 0 6z"/>
                                        </svg>
                                        <span>{{ $quote->customer->city }}, {{ $quote->customer->country->name }}</span>
                                    </div>
                                    @if($quote->listing->phone)
                                        <div class="contact-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                                                <path d="M3.654 1.328a.678.678 0 0
                                                    0-1.015-.063L1.605 2.3c-.483.484-.661
                                                    1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569
                                                    0 0 0 6.608 4.168c.601.211 1.286.033
                                                    1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678
                                                    0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745
                                                    1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654
                                                    1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29
                                                    2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0
                                                    .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745
                                                    0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034
                                                    1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42
                                                    18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                                            </svg>
                                            <span>{{ $quote->listing->phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        /* Improve layout for card columns */
        .card .row {
            margin-left: 0;
            margin-right: 0;
            display: flex;
            flex-wrap: wrap;
        }

        /* Left column styles */
        .col-md-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
            position: relative;
        }

        /* Right column styles */
        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            position: relative;
        }

        /* Add border between columns */
        .contact-sidebar {
            border-left: 1px solid #e2e8f0;
            padding-left: 1.5rem;
            height: 100%;
        }

        .detail-group {
            margin-bottom: 1rem;
        }
        
        .detail-group label {
            display: block;
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            font-size: 1rem;
        }
        
        .quote-details-text {
            white-space: pre-line;
        }
        
        .contact-details, .address-details, .customer-details {
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .contact-item svg {
            margin-right: 0.5rem;
            min-width: 16px;
        }
        
        .attachment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
        }
        
        .attachment-item {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            overflow: hidden;
        }
        
        .attachment-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .attachment-thumbnail {
            width: 100%;
            height: 80px;
            object-fit: cover;
        }
        
        .video-thumbnail, .document-thumbnail {
            width: 100%;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        
        .attachment-name {
            padding: 0.5rem;
            text-align: center;
            font-size: 0.75rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        @media (max-width: 767px) {
            .col-md-8, .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .contact-sidebar {
                border-left: none;
                border-top: 1px solid #e2e8f0;
                padding-left: 0;
                margin-top: 2rem;
                padding-top: 2rem;
            }
            
            .pe-md-4 {
                padding-right: 0 !important;
            }
        }
    </style>
</x-app-layout>