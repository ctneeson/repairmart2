<x-app-layout title="Create Quote">
    <main>
        <div class="container-small">
            <h1 class="listing-details-page-title">Create a Quote</h1>
            <!-- Listing Summary Section -->
            <div class="card p-large mb-large">
                <div class="row">
                    <div class="col-md-8 pe-md-4">
                        <h2 class="mb-small">Listing Details</h2>

                        <div class="quote-details">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label>Title</label>
                                        <h3>{{ $listing->title }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h3><a href="{{ route('listings.show', $listing->id) }}"
                                            class="btn btn-outline-secondary btn-sm mt-2"
                                            target="_blank"
                                            rel="noopener">
                                            View Listing
                                        </a>
                                    </h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="detail-group">
                                        <label>Product Classification</label>
                                        @foreach($listing->products as $product)
                                        <div class="detail-value">
                                                {{ $product->category }} > {{ $product->subcategory }}
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label>Manufacturer</label>
                                        <div class="detail-value">
                                            {{ $listing->manufacturer->name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-group">
                                        <label>Budget</label>
                                        <div class="detail-value">
                                            {{ $listing->currency->iso_code}} {{ number_format($listing->budget, 2) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-group">
                                        <label>Expiry</label>
                                        <div class="detail-value">
                                            {{ $listing->created_at
                                            ->addDays($listing->expiry)
                                            ->format('Y-m-d') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="detail-group">
                                        <label>Description</label>
                                        <div class="detail-value">
                                            {{ $listing->description}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="contact-sidebar">
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
                                        <span>{{ $listing->customer->name }}</span>
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
                                            {{ $listing->address_line1 }}{{ !empty($listing->address_line2)
                                                                                    ? ', ' . $listing->address_line2 : '' }}
                                            <br>
                                            {{ $listing->city }}, {{ $listing->country->name }}
                                            <br>
                                            {{ $listing->postcode }}
                                        </span>
                                    </div>
                                    @if($listing->phone)
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
                                            <span>{{ $listing->phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($listing->attachments->count() > 0)
                            <div class="listing-attachments mb-small">
                                <h3 class="mb-2">Attachments</h3>
                                <div class="attachment-grid">
                                    @foreach($listing->attachments as $attachment)
                                        <div class="attachment-item">
                                            <a href="{{ Storage::url($attachment->path) }}" target="_blank" class="attachment-link">
                                                @if(Str::contains($attachment->mime_type, 'image'))
                                                    <img src="{{ Storage::url($attachment->path) }}" alt="{{ $attachment->filename }}" class="attachment-thumbnail">
                                                @elseif(Str::contains($attachment->mime_type, 'video'))
                                                    <div class="video-thumbnail">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-film" viewBox="0 0 16 16">
                                                            <path d="M0 1a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V1zm4 0v6h8V1H4zm8 8H4v6h8V9zM1 1v2h2V1H1zm2 3H1v2h2V4zM1 7v2h2V7H1zm2 3H1v2h2v-2zm-2 3v2h2v-2H1zM15 1h-2v2h2V1zm-2 3v2h2V4h-2zm2 3h-2v2h2V7zm-2 3v2h2v-2h-2zm2 3h-2v2h2v-2z"/>
                                                        </svg>
                                                    </div>
                                                @elseif(Str::contains($attachment->mime_type, 'pdf'))
                                                    <div class="document-thumbnail">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-pdf" viewBox="0 0 16 16">
                                                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                                            <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="document-thumbnail">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-text" viewBox="0 0 16 16">
                                                            <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1H5z"/>
                                                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="attachment-name">{{ Str::limit($attachment->filename, 15) }}</div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="text-center p-4 text-muted">
                                <p>No attachments were added to the listing.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-small">
            <h2 class="mb-small">Quote Details</h2>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form action="{{ route('quotes.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="card add-new-listing-form">
                @csrf
                <!-- Hidden Fields -->
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                <input type="hidden" name="status_id" value="1">

                
                <div class="form-content">
                    <div class="form-details">
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('currency_id') has-error @enderror">
                                    <label for="currency_id">Currency</label>
                                    <x-select-currency-all name="currency_id" value="{{ old('currency_id') }}" />
                                    @error('currency_id')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group @error('amount') has-error @enderror">
                                    <label for="amount">Amount</label>
                                    <input type="number" 
                                        name="amount" 
                                        id="amount"  
                                        value="{{ old('amount') }}" 
                                        step="0.01" 
                                        min="0.01" 
                                        required>
                                    @error('amount')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('deliverymethod_id') has-error @enderror">
                                    <label for="deliverymethod_id">Delivery Method</label>
                                    <select name="deliverymethod_id" id="deliverymethod_id" required>
                                        <option value="">Select a delivery method</option>
                                        @foreach($deliveryMethods as $method)
                                            <option value="{{ $method->id }}" {{ old('deliverymethod_id') == $method->id ? 'selected' : '' }}>
                                                {{ $method->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('deliverymethod_id')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group @error('turnaround') has-error @enderror">
                                    <label for="turnaround">Turnaround (Days)</label>
                                    <input type="number" 
                                        name="turnaround" 
                                        id="turnaround" 
                                        value="{{ old('turnaround') }}" 
                                        min="1" 
                                        step="1" 
                                        required>
                                    @error('turnaround')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('description') has-error @enderror">
                                    <label for="description">Quote Description</label>
                                    <textarea
                                        name="description"
                                        id="description"
                                        rows="4"
                                        placeholder="Describe your repair service, approach, and any other information the customer should know">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Address Fields -->
                        <div id="address-fields" class="{{ old('use_default_location', '1') == '1' ? 'opacity-50' : '' }}">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group mt-4">
                                        <div class="checkbox">
                                            <input type="hidden" name="use_default_location" value="0">
                                            <input
                                                type="checkbox"
                                                name="use_default_location"
                                                id="use-default-location"
                                                value="1"
                                                {{ old('use_default_location', '1') == '1' ? 'checked' : '' }}
                                            >
                                            <label for="use-default-location">Use My Default Address</label>
                                        </div>
                                    </div>
                                    <div class="form-group @error('address_line1') has-error @enderror">
                                        <label for="address_line1">Address Line 1</label>
                                        <input
                                            type="text"
                                            name="address_line1"
                                            id="address_line1"
                                            value="{{ old('address_line1', $user->address_line1) }}"
                                            {{ old('use_default_location', '1') == '1' ? 'readonly' : '' }}
                                            required>
                                        @error('address_line1')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group @error('address_line2') has-error @enderror">
                                        <label for="address_line2">Address Line 2</label>
                                        <input
                                            type="text"
                                            name="address_line2"
                                            id="address_line2"
                                            value="{{ old('address_line2', $user->address_line2) }}"
                                            {{ old('use_default_location', '1') == '1' ? 'readonly' : '' }}>
                                        @error('address_line2')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('city') has-error @enderror">
                                        <label for="city">City</label>
                                        <input
                                            type="text"
                                            name="city"
                                            id="city"
                                            value="{{ old('city', $user->city) }}"
                                            {{ old('use_default_location', '1') == '1' ? 'readonly' : '' }}
                                            required>
                                        @error('city')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group @error('postcode') has-error @enderror">
                                        <label for="postcode">Postcode</label>
                                        <input
                                            type="text"
                                            name="postcode"
                                            id="postcode"
                                            value="{{ old('postcode', $user->postcode) }}"
                                            {{ old('use_default_location', '1') == '1' ? 'readonly' : '' }}
                                            required>
                                        @error('postcode')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('country_id') has-error @enderror">
                                        <label for="country_id">Country</label>
                                        <!-- IMPORTANT: Always include a hidden country_id field, we'll toggle which one is used -->
                                        <input type="hidden" 
                                            name="country_id" 
                                            id="hidden_country_id" 
                                            value="{{ old('country_id', $user->country_id) }}"
                                            {{ old('use_default_location', '1') != '1' ? 'disabled' : '' }}>
                                        
                                        <x-select-country-all 
                                            name="{{ old('use_default_location', '1') == '1' ? '_country_id' : 'country_id' }}"
                                            id="country_id"
                                            value="{{ old('country_id', $user->country_id) }}" 
                                            :disabled="old('use_default_location', '1') == '1'"
                                            required="true" />
                                        @error('country_id')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group @error('phone') has-error @enderror">
                                        <label for="phone">Phone <small>(Optional)</small></label>
                                        <input
                                            type="text"
                                            name="phone"
                                            id="phone"
                                            value="{{ old('phone', $user->phone) }}"
                                            {{ old('use_default_location', '1') == '1' ? 'readonly' : '' }}>
                                        @error('phone')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-attachments">
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
                                id="quoteFormAttachmentUpload" 
                                type="file" 
                                name="attachments[]" 
                                multiple 
                                accept="image/*,video/*,application/pdf,application/msword,
                                        application/vnd.openxmlformats-officedocument.
                                        wordprocessingml.document,text/plain" 
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

                        <div id="attachmentPreviews" class="quote-form-attachments"></div>
                        <div id="attachmentsList" style="margin-top: 20px;"></div>

                        <p class="info-message">
                            <small>
                                Supported formats: Images, Videos, PDF, DOC, DOCX, TXT<br>
                                Maximum total upload size: {{ ini_get('post_max_size') }}<br>
                                Maximum individual file size: {{ ini_get('upload_max_filesize') }}
                            </small>
                        </p>
                    </div>
                </div>
                <div class="p-medium" style="width: 100%">
                    <div class="flex justify-end gap-1">
                        <button type="button" id="cancel-btn" class="btn btn-default"
                            data-return-url="{{ route('listings.show', $listing->id) }}">
                            Cancel
                        </button>
                        {{-- <a href="{{ route('listings.show', $listing->id) }}"
                            class="btn btn-default">Cancel</a> --}}
                        <button type="button" class="btn btn-default" id="reset-button">Reset</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    @push('scripts')
    <!-- User data required for the reset functionality -->
    <div id="user-data" 
         data-user="{{ json_encode([
             'address_line1' => $user->address_line1,
             'address_line2' => $user->address_line2,
             'city' => $user->city,
             'postcode' => $user->postcode,
             'phone' => $user->phone,
             'country_id' => $user->country_id
         ]) }}" 
         hidden>
    </div>
    @endpush

    @vite(['resources/js/quote-create.js'])

</x-app-layout>