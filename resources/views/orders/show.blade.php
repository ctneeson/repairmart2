<x-app-layout title="Order Details">
    <div class="container-small">
        @if(session('success'))
            <div class="alert alert-success mb-large">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger mb-large">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title mb-0">Order #{{ $order->id }}</h1>
            <div class="order-status">
                <span class="badge bg-{{ $order->status->color }}">{{ $order->status->name }}</span>
            </div>
        </div>
        
        <div class="card p-large mb-large">
            <div class="row">
                <!-- Left Column: Order Information -->
                <div class="col-md-8 pe-md-4">
                    <h2 class="mb-small">Order Details</h2>
                    
                    <div class="quote-details">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label>Amount</label>
                                    <div class="detail-value">
                                        @if(auth()->user()->id === $order->specialist_id && $order->status_id === 5)
                                            <form action="{{ route('orders.update-amount', $order) }}" method="POST" class="amount-edit-form">
                                                @csrf
                                                @method('PATCH')
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ $order->currency->iso_code }}</span>
                                                    <input type="number" name="amount" value="{{ $order->amount }}" step="0.01" min="0" class="form-control" required>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        @else
                                            {{ $order->currency->iso_code }} {{ number_format($order->amount, 2) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label>Estimated Completion</label>
                                    <div class="detail-value">
                                        {{ $order->quote->turnaround }} days
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="detail-group">
                                    <label>Delivery Method</label>
                                    <div class="detail-value">
                                        {{ $order->deliveryMethod->name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="detail-group">
                                    <label>Quote Description</label>
                                    <div class="detail-value quote-description-text">
                                        {!! nl2br(e($order->quote->description)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($order->quote->attachments->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h3 class="mb-small">Quote Attachments</h3>
                                    <div class="attachment-grid">
                                        @foreach($order->quote->attachments as $attachment)
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
                </div>

                <!-- Right Column: Contact Information -->
                <div class="col-md-4">
                    <div class="contact-sidebar">
                        <h2 class="mb-small">Listing Details</h2>
                        <h3>{{ $order->quote->listing->title }}
                            <a href="{{ route('listings.show', $order->quote->listing_id) }}" 
                                class="btn btn-outline-secondary btn-sm mt-2" 
                                target="_blank" 
                                rel="noopener">
                                    view listing
                                </a>
                        </h3>
                        <div class="listing-meta">
                            <span class="badge bg-info">{{ $order->quote->listing->manufacturer->name }}</span>
                            @foreach($order->quote->listing->products as $product)
                                <span class="badge bg-secondary">{{ $product->category }} > {{ $product->subcategory }}</span>
                            @endforeach
                        </div>
                        @if($order->quote->listing->primaryAttachment)
                        <div class="listing-thumbnail mb-small">
                            @if(Str::contains($order->quote->listing->primaryAttachment->mime_type, 'image'))
                            <img src="{{ Storage::url($order->quote->listing->primaryAttachment->path) }}"
                                alt="{{ $order->quote->listing->title }}"
                                class="img-fluid"
                                style="max-height: 150px; max-width: 100%; object-fit: contain;">
                            @elseif(Str::contains($order->quote->listing->primaryAttachment->mime_type, 'video'))
                            <video controls class="img-fluid"
                                style="max-height: 150px; max-width: 100%;">
                                <source src="{{ Storage::url($order->quote->listing->primaryAttachment->path) }}"
                                        type="{{ $order->quote->listing->primaryAttachment->mime_type }}">
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
                                        <a href="{{ route('profile.show', $order->repairSpecialist) }}"
                                        class="text-blue-600 hover:underline"
                                            >
                                            {{ $order->repairSpecialist->name }}
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
                                        {{ $order->quote->address_line1 }}{{ !empty($order->quote->address_line2)
                                                                            ? ', ' . $order->quote->address_line2 : '' }}
                                        <br>
                                        {{ $order->quote->city }}, {{ $order->quote->country->name }}
                                        <br>
                                        {{ $order->quote->postcode }}
                                    </span>
                                </div>
                                @if($order->quote->phone)
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
                                        <span>{{ $order->quote->phone }}</span>
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
                                        <a href="{{ route('profile.show', $order->customer) }}"
                                            class="text-blue-600 hover:underline"
                                                >
                                                {{ $order->customer->name }}
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
                                        {{ $order->quote->listing->address_line1 }}{{ !empty($order->quote->listing->address_line2)
                                                                                ? ', ' . $order->quote->listing->address_line2 : '' }}
                                        <br>
                                        {{ $order->quote->listing->city }}, {{ $order->quote->listing->country->name }}
                                        <br>
                                        {{ $order->quote->listing->postcode }}
                                    </span>
                                </div>
                                @if($order->quote->listing->phone)
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
                                        <span>{{ $order->quote->listing->phone }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Management Section -->
        <div class="card p-large mb-large">
            <div class="row">
                <div class="col-md-8 pe-md-4">
                    <div class="comments-list">
                        @if($order->comments->count() > 0)
                        <div class="table-responsive" style="width: 95%;">
                            <h3 class="mb-3">Comment History</h3>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Comment</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->comments as $comment)
                                        <tr class="{{ $comment->user_id === auth()->id() ? 'table-primary' : '' }}">
                                            <td>
                                                @if($comment->user_id === auth()->id())
                                                    <span class="badge bg-primary">You</span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <a href="{{ route('profile.show', $comment->user) }}"
                                                            class="text-blue-600 hover:underline">
                                                            {{ $comment->user->name }}
                                                        </a>
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $comment->comment }}</td>
                                            <td>{{ $comment->created_at->format('M j, Y g:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="alert alert-info">No comments have been added to this order yet.</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-sidebar">
                        <div class="form-attachments" style="border-left: none; padding-left: 0; padding-right: 1.75rem;">
                            <h3 class="mb-3">Attachments</h3>
                            <div class="form-attachment-upload">
                                <div class="upload-placeholder">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        style="width: 48px; height: 48px;"
                                    >
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="1.5" fill="none"/>
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 8v8m-4-4h8"
                                        />
                                    </svg>
                                </div>
                                <input 
                                    id="listingFormAttachmentUpload" 
                                    type="file" 
                                    name="attachments[]" 
                                    multiple 
                                    accept="image/*,video/*" 
                                    data-max-post-size="{{ ini_get('post_max_size') }}"
                                    data-max-file-size="{{ ini_get('upload_max_filesize') }}"
                                />
                            </div>

                            @error('attachments')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                            
                            @error('attachments.*')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                
                            <div id="attachmentPreviews" class="listing-form-attachments"></div>
                            <div id="attachmentsList" style="margin-top: 20px;"></div>
                            
                            <p class="info-message">
                                <small>
                                    Supported formats: JPEG, PNG, JPG, GIF, SVG, MP4, MOV, OGG, QT<br>
                                    Maximum total upload size: {{ ini_get('post_max_size') }}<br>
                                    Maximum individual file size: {{ ini_get('upload_max_filesize') }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Section -->
            @if($order->status_id === 7) {{-- Closed status --}}
                <div class="feedback-section mt-4">
                    <h3>Order Feedback</h3>
                    <div class="row mt-3">
                        @if(auth()->id() === $order->customer_id && empty($order->customer_feedback_id))
                            <div class="col-md-6">
                                <div class="card p-3">
                                    <h4>Customer Feedback</h4>
                                    <form action="{{ route('orders.feedback', $order) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="feedback_type" class="form-label">Rating</label>
                                            <select name="feedback_type_id" id="feedback_type" class="form-select" required>
                                                <option value="">Select a rating</option>
                                                @foreach($feedbackTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="feedback_comment" class="form-label">Comment</label>
                                            <textarea name="feedback" id="feedback_comment" rows="3" 
                                                class="form-control" maxlength="255" required></textarea>
                                        </div>
                                        <input type="hidden" name="feedback_type" value="customer">
                                        <button type="submit" class="btn btn-primary">Submit Feedback</button>
                                    </form>
                                </div>
                            </div>
                        @elseif(auth()->id() === $order->customer_id && !empty($order->customer_feedback_id))
                            <div class="col-md-6">
                                <div class="card p-3">
                                    <h4>Your Feedback</h4>
                                    <div class="feedback-rating mb-2">
                                        <strong>Rating:</strong> {{ $order->customerFeedbackType->name }}
                                    </div>
                                    <div class="feedback-comment">
                                        <strong>Comment:</strong> {{ $order->customer_feedback }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if(auth()->id() === $order->specialist_id && empty($order->specialist_feedback_id))
                            <div class="col-md-6">
                                <div class="card p-3">
                                    <h4>Specialist Feedback</h4>
                                    <form action="{{ route('orders.feedback', $order) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="feedback_type" class="form-label">Rating</label>
                                            <select name="feedback_type_id" id="feedback_type" class="form-select" required>
                                                <option value="">Select a rating</option>
                                                @foreach($feedbackTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="feedback_comment" class="form-label">Comment</label>
                                            <textarea name="feedback" id="feedback_comment" rows="3" 
                                                class="form-control" maxlength="255" required></textarea>
                                        </div>
                                        <input type="hidden" name="feedback_type" value="specialist">
                                        <button type="submit" class="btn btn-primary">Submit Feedback</button>
                                    </form>
                                </div>
                            </div>
                        @elseif(auth()->id() === $order->specialist_id && !empty($order->specialist_feedback_id))
                            <div class="col-md-6">
                                <div class="card p-3">
                                    <h4>Your Feedback</h4>
                                    <div class="feedback-rating mb-2">
                                        <strong>Rating:</strong> {{ $order->specialistFeedbackType->name }}
                                    </div>
                                    <div class="feedback-comment">
                                        <strong>Comment:</strong> {{ $order->specialist_feedback }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Display other party's feedback if it exists -->
                        @if(auth()->id() === $order->customer_id && !empty($order->specialist_feedback_id))
                            <div class="col-md-6">
                                <div class="card p-3">
                                    <h4>Specialist's Feedback</h4>
                                    <div class="feedback-rating mb-2">
                                        <strong>Rating:</strong> {{ $order->specialistFeedbackType->name }}
                                    </div>
                                    <div class="feedback-comment">
                                        <strong>Comment:</strong> {{ $order->specialist_feedback }}
                                    </div>
                                </div>
                            </div>
                        @elseif(auth()->id() === $order->specialist_id && !empty($order->customer_feedback_id))
                            <div class="col-md-6">
                                <div class="card p-3">
                                    <h4>Customer's Feedback</h4>
                                    <div class="feedback-rating mb-2">
                                        <strong>Rating:</strong> {{ $order->customerFeedbackType->name }}
                                    </div>
                                    <div class="feedback-comment">
                                        <strong>Comment:</strong> {{ $order->customer_feedback }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <div class="card p-large mb-large">
            <div class="row">
                <div class="col-md-8 pe-md-4">
                    <h3 class="mb-3">Comments</h3>
                    @if($order->status_id != 7) {{-- Not Closed --}}
                    <div class="comment-form mb-4">
                        <form action="{{ route('orders.comments.store', $order) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <span class="label">Add a comment</span>
                                <textarea name="comment" id="comment" rows="1" 
                                    class="form-control" placeholder="Enter your comment here..." 
                                    maxlength="255" required style="width: 95%;"></textarea>
                            </div>
                            <div class="d-flex align-center mt-2">
                                <button type="submit" class="btn btn-primary">
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
                <div class="col-md-4">
                    <div class="contact-sidebar">
                        <h3 class="mb-3">Manage Status</h3>
                        <span class="label">Current Status:</span> 
                        <span class="badge bg-{{ $order->status->color }} fs-6">{{ $order->status->name }}</span>
                    
                        @php
                            $userRole = auth()->user()->hasRole('specialist') ? 'specialist' : 'customer';
                            $allowedTransitions = \App\Models\OrderStatusTransition::where('role_id', auth()->user()->roles()->first()->id)
                                ->where('from_status_id', $order->status_id)
                                ->pluck('to_status_id')
                                ->toArray();
                            $allowedStatuses = \App\Models\OrderStatus::whereIn('id', $allowedTransitions)->get();
                        @endphp
                        
                        @if(count($allowedStatuses) > 0)
                            <form action="{{ route('orders.update-status', $order) }}" method="POST" id="statusUpdateForm">
                                @csrf
                                @method('PATCH')
                                <div class="form-group">
                                    {{-- <label for="status_id" class="form-label">Change Status</label> --}}
                                    <select name="status_id" id="status_id" class="form-select">
                                        @foreach($allowedStatuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-flex align-center mt-3">
                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info">
                                No status changes are available for your role at this time.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/listings-attachments.js',])

</x-app-layout>