<x-app-layout title="View Quote">
    <main>
        <div class="container-small">
            <div class="flex items-center">
                <h1 class="quote-details-page-title">Quote #{{ $quote->id }}</h1>
                <span class="quote-status-badge status-{{ strtolower(str_replace(' ', '-', $quote->status->name)) }}">
                    {{ $quote->status->name }}
                </span>
            </div>

            <!-- Quote Details Card -->
            <div class="card p-large my-medium">
                <div class="row">
                    <!-- Left Column: Quote Information -->
                    <div class="col-md-8 pe-md-4">
                        <h2 class="mb-small">Quote Details</h2>
                        
                        <div class="quote-details">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label style="font-weight: bold">Amount</label>
                                        <div class="detail-value quote-description-text">
                                            {{ $quote->currency->iso_code}} {{ number_format($quote->amount, 2) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label style="font-weight: bold">Turnaround Time</label>
                                        <div class="detail-value quote-description-text">
                                            {{ $quote->turnaround }} {{ Str::plural('day', $quote->turnaround) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label style="font-weight: bold">Delivery Method</label>
                                        <div class="detail-value quote-description-text">
                                            {{ $quote->deliveryMethod->name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label style="font-weight: bold">Created On</label>
                                        <div class="detail-value quote-description-text">
                                            {{ $quote->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="detail-group">
                                        <label style="font-weight: bold">Quote Description</label>
                                        <div class="detail-value quote-description-text">
                                            {!! nl2br(e($quote->description)) !!}
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
                                                        @elseif(Str::startsWith($attachment->mime_type, 'video/'))
                                                            <div class="video-player-container">
                                                                <video 
                                                                    src="{{ Storage::url($attachment->path) }}" 
                                                                    class="video-preview" 
                                                                    muted 
                                                                    loop 
                                                                    preload="metadata"
                                                                    onclick="event.preventDefault(); this.paused ? this.play() : this.pause();">
                                                                </video>
                                                                <div class="video-overlay">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" 
                                                                            class="bi bi-play-circle" viewBox="0 0 16 16">
                                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                                        <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445z"/>
                                                                    </svg>
                                                                </div>
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
                    </div>

                    <!-- Right Column: Contact Information -->
                    <div class="col-md-4">
                        <div class="contact-sidebar">
                            <h2 class="mb-small">Listing Details</h2>
                            <table class="listing-details-table-header">
                                <tbody>
                                    <tr>
                                        <th><h3>{{ $quote->listing->title }}</h3></th>
                                        <td>
                                            <a href="{{ route('listings.show', $quote->listing_id) }}" 
                                                class="btn" 
                                                target="_blank" 
                                                rel="noopener">
                                                view
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="listing-details-table-small">
                                <tbody>
                                    <tr>
                                        <th>Manufacturer</th>
                                        <td>{{ $quote->listing->manufacturer->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Product(s)</th>
                                    @foreach($quote->listing->products as $product)
                                        <td colspan="2">{{ $product->category }} > {{ $product->subcategory }}</td>
                                    @endforeach
                                    </tr>
                                </tbody>
                            </table>
                            @if($quote->listing->primaryAttachment)
                            <div class="listing-thumbnail mb-small">
                                @if(Str::contains($quote->listing->primaryAttachment->mime_type, 'image'))
                                <img src="{{ Storage::url($quote->listing->primaryAttachment->path) }}"
                                    alt="{{ $quote->listing->title }}"
                                    class="img-fluid"
                                    style="max-height: 150px; max-width: 100%; object-fit: contain;">
                                @elseif(Str::contains($quote->listing->primaryAttachment->mime_type, 'video'))
                                <video controls class="img-fluid"
                                    style="max-height: 150px; max-width: 100%;">
                                    <source src="{{ Storage::url($quote->listing->primaryAttachment->path) }}"
                                            type="{{ $quote->listing->primaryAttachment->mime_type }}">
                                        Your browser doesn't support video playback.
                                </video>
                                @endif
                            </div>
                            @endif

                            <!-- Repair Specialist Information -->
                            <div class="contact-details mb-4">
                                <h3>Specialist</h3>
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
                                        <span>
                                            <a href="{{ route('profile.show', $quote->repairSpecialist) }}"
                                            class="text-blue-600 hover:underline"
                                              >
                                              {{ $quote->repairSpecialist->name }}
                                            </a>
                                        </span>
                                    </div>
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
                                            <span>
                                                {{ $quote->address_line1 }}{{ !empty($quote->address_line2)
                                                                             ? ', ' . $quote->address_line2 : '' }}
                                                <br>
                                                {{ $quote->city }}, {{ $quote->country->name }}
                                                <br>
                                                {{ $quote->postcode }}
                                            </span>
                                        </div>
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

                            <!-- Customer Information -->
                            <div class="customer-details">
                                <h3>Customer</h3>
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
                                        <span>
                                            <a href="{{ route('profile.show', $quote->customer) }}"
                                                class="text-blue-600 hover:underline"
                                                  >
                                                  {{ $quote->customer->name }}
                                            </a>
                                        </span>
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
                                        <span>
                                            {{ $quote->listing->address_line1 }}{{ !empty($quote->listing->address_line2)
                                                                                    ? ', ' . $quote->listing->address_line2 : '' }}
                                            <br>
                                            {{ $quote->listing->city }}, {{ $quote->listing->country->name }}
                                            <br>
                                            {{ $quote->listing->postcode }}
                                        </span>
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
            <div class="quote-actions-right">
                @if (auth()->id() === $quote->customer->id)
                <a href="{{ route('email.create', ['quote_id' => $quote->id, 'recipient_ids[]' => $quote->user_id]) }}"
                    class="listing-details-email btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    Contact Repair Specialist
                </a>
                @endif
                @if(auth()->id() === $quote->customer->id && $quote->status_id === 1)
                <a href="{{ route('orders.create', $quote) }}" class="btn btn-primary"
                    style="margin-bottom: 0.1rem;">
                    Accept Quote
                </a>
                @endif
            
                @if(auth()->id() === $quote->user_id && $quote->status_id === 1)
                    <a href="{{ route('quotes.edit', $quote->id) }}" class="btn btn-primary">
                        Edit Quote
                    </a>
                @endif
                
            </div>
        </div>
    </main>
</x-app-layout>