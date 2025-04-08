<x-app-layout title="Create Quote">
    <main>
        <div class="container-small">
            <h1 class="listing-details-page-title">Create a Quote</h1>

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
                class="card p-large">
                @csrf
                <!-- Hidden Fields -->
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                <input type="hidden" name="status_id" value="1">

                <div class="form-section mb-large">
                    <h2 class="mb-small">Listing Details</h2>
                    <div class="listing-summary card p-medium mb-medium">
                        <div style="display: flex; flex-wrap: wrap; width: 100%;">
                            <div style="flex: 1; min-width: 50%; padding-right: 15px;">
                                <!-- Left Column: Listing Details -->
                                <div class="col-md-6" style="padding-right: 15px;"> <!-- Added explicit padding -->
                                    <h3>{{ $listing->title }}<a href="{{ route('listings.show', $listing->id) }}"
                                        class="btn btn-outline-secondary btn-sm mt-2">view listing</a>
                                    </h3>
                                    
                                    <!-- Listing Thumbnail -->
                                    @if($listing->primaryAttachment)
                                    <div class="listing-thumbnail mb-small">
                                        @if(Str::contains($listing->primaryAttachment->mime_type, 'image'))
                                        <img src="{{ Storage::url($listing->primaryAttachment->path) }}"
                                            alt="{{ $listing->title }}"
                                            class="img-fluid"
                                            style="max-height: 150px; max-width: 100%; object-fit: contain;">
                                        @elseif(Str::contains($listing->primaryAttachment->mime_type, 'video'))
                                        <video controls class="img-fluid"
                                            style="max-height: 150px; max-width: 100%;">
                                            <source src="{{ Storage::url($listing->primaryAttachment->path) }}"
                                                    type="{{ $listing->primaryAttachment->mime_type }}">
                                                Your browser doesn't support video playback.
                                        </video>
                                        @endif
                                    </div>
                                    @endif
                    
                                    <p>{{ \Illuminate\Support\Str::limit($listing->description, 150) }}</p>
                                
                                    <div class="listing-meta">
                                        <span class="badge bg-info">{{ $listing->manufacturer->name }}</span>
                                        @foreach($listing->products as $product)
                                            <span class="badge bg-secondary">{{ $product->category }} > {{ $product->subcategory }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div style="flex: 1; min-width: 50%; padding-left: 15px; border-left: 1px solid #eee;">
                                <!-- Right Column: Customer Information -->
                                <div class="col-md-6" style="padding-left: 15px; border-left: 1px solid #eee;"> <!-- Added left border and padding -->
                                    <div class="customer-details">
                                        <h4>Customer Information</h4>
                                        <ul class="list-unstyled">
                                            @if($listing->customer->name)
                                            <li><strong>Name:</strong> {{ $listing->customer->name }}</li>
                                            @endif
                                            @if($listing->customer->city)
                                            <li><strong>City:</strong> {{ $listing->customer->city }}</li>
                                            @endif
                                            @if($listing->customer->country)
                                            <li><strong>Country:</strong> {{ $listing->customer->country->name }}</li>
                                            @endif
                                            @if($listing->customer->phone)
                                            <li><strong>Phone:</strong> {{ $listing->customer->phone }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-content">
                    <!-- Quote Details Section -->
                    <div class="form-section mb-large">
                        <h2 class="mb-small">Quote Details</h2>
                    
                        <div class="form-details">
                            <!-- First row: Currency and Amount -->
                            <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                                <!-- Currency Dropdown (2/3 width) -->
                                <div style="flex: 2;">
                                    <div class="form-group @error('currency_id') has-error @enderror">
                                        <label for="currency_id">Currency</label>
                                        <x-select-currency-all name="currency_id" value="{{ old('currency_id') }}" />
                                        @error('currency_id')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Amount Input (1/3 width) -->
                                <div style="flex: 1;">
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
                            
                            <!-- Second row: Delivery Method and Turnaround -->
                            <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                                <!-- Delivery Method Dropdown (2/3 width) -->
                                <div style="flex: 2;">
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
                                
                                <!-- Turnaround Time Input (1/3 width) -->
                                <div style="flex: 1;">
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
                            
                            <!-- Third row: Quote Description -->
                            <div class="form-row" style="margin-bottom: 15px;">
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
                            
                            <!-- Location Information -->
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
                            
                            <!-- Address Fields - Apply the same flexbox approach -->
                            <div id="address-fields" class="{{ old('use_default_location', '1') == '1' ? 'opacity-50' : '' }}">
                                <!-- Address Line 1 and Postcode row -->
                                <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                                    <div style="flex: 2;">
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
                                    </div>
                                    <div style="flex: 1;">
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
                                
                                <!-- Address Line 2 and Country row -->
                                <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                                    <div style="flex: 2;">
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
                                    <div style="flex: 1;">
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
                                                id="visible_country_id"
                                                value="{{ old('country_id', $user->country_id) }}" 
                                                :disabled="old('use_default_location', '1') == '1'"
                                                required="true" />
                                            @error('country_id')
                                                <p class="error-message">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- City and Phone row -->
                                <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                                    <div style="flex: 2;">
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
                                    <div style="flex: 1;">
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
                    </div>
                        <!-- Attachments -->
                        <div class="form-group mt-large">
                            <label>Attachments</label>
                            <div class="form-attachments">
                                <div class="form-attachment-upload">
                                    <div class="upload-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0
                                                0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        <span>Drag and drop files here or click to upload</span>
                                    </div>
                                    <input 
                                      id="quoteFormAttachmentUpload" 
                                      type="file" 
                                      name="attachments[]" 
                                      multiple 
                                      accept="image/*,video/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain" 
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
                    </div>
                </div>
                <div class="p-medium" style="width: 100%">
                    <div class="flex justify-end gap-1">
                        <a href="{{ route('listings.show', $listing->id) }}"
                            class="btn btn-default">Cancel</a>
                        <button type="button" class="btn btn-default" id="reset-button">Reset</button>
                        <button type="submit" class="btn btn-primary">Submit Quote</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle the default location checkbox
            const useDefaultLocationCheckbox = document.getElementById('use-default-location');
            const addressFields = document.getElementById('address-fields');
            const addressInputs = addressFields.querySelectorAll('input, select');
            
            function updateAddressFieldsState() {
                const useDefault = useDefaultLocationCheckbox.checked;
                
                // Update the visual appearance
                if (useDefault) {
                    addressFields.classList.add('opacity-50');
                } else {
                    addressFields.classList.remove('opacity-50');
                }
                
                // Handle the country ID fields specifically
                const hiddenCountryId = document.getElementById('hidden_country_id');
                const visibleCountryId = document.getElementById('visible_country_id');
                
                if (useDefault) {
                    // When using default address, enable the hidden field and disable the visible dropdown
                    hiddenCountryId.disabled = false;
                    hiddenCountryId.value = '{{ $user->country_id }}';
                    visibleCountryId.name = '_country_id'; // Change the name so it's not submitted
                    visibleCountryId.disabled = true;
                } else {
                    // When manually entering address, disable the hidden field and enable the visible dropdown
                    hiddenCountryId.disabled = true;
                    visibleCountryId.name = 'country_id'; // Set the proper name for submission
                    visibleCountryId.disabled = false;
                }
                
                // Update each input's readonly status
                addressInputs.forEach(input => {
                    if (input.id !== 'hidden_country_id' && input.id !== 'visible_country_id') {
                        input.readOnly = useDefault;
                    }
                });
            }
            
            // Initialize and set up change handler
            updateAddressFieldsState();
            useDefaultLocationCheckbox.addEventListener('change', updateAddressFieldsState);
            
            // Reset button functionality
            const resetButton = document.getElementById('reset-button');
            if (resetButton) {
                resetButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (!confirm("Are you sure you want to reset the form? All entered information will be cleared.")) {
                        return;
                    }
                    
                    // Reset form fields
                    document.getElementById('amount').value = '';
                    document.getElementById('turnaround').value = '';
                    document.getElementById('deliverymethod_id').selectedIndex = 0;
                    document.getElementById('description').value = '';
                    
                    // Reset the currency selector
                    const currencySelect = document.querySelector('select[name="currency_id"]');
                    if (currencySelect) {
                        currencySelect.selectedIndex = 0;
                    }
                    
                    // Reset default location to checked
                    document.getElementById('use-default-location').checked = true;
                    updateAddressFieldsState();
                    
                    // Reset address to user defaults
                    document.getElementById('address_line1').value = '{{ $user->address_line1 }}';
                    document.getElementById('address_line2').value = '{{ $user->address_line2 }}';
                    document.getElementById('city').value = '{{ $user->city }}';
                    document.getElementById('postcode').value = '{{ $user->postcode }}';
                    document.getElementById('phone').value = '{{ $user->phone }}';
                    
                    // Reset country
                    const countrySelect = document.querySelector('select[name="country_id"]');
                    if (countrySelect) {
                        // Find the option with the user's country ID
                        const userCountryOption = countrySelect.querySelector('option[value="{{ $user->country_id }}"]');
                        if (userCountryOption) {
                            userCountryOption.selected = true;
                        }
                    }
                    
                    // Reset attachments
                    const fileInput = document.getElementById('quoteFormAttachmentUpload');
                    if (fileInput) {
                        fileInput.value = '';
                        
                        // Clear previews
                        const previewsContainer = document.getElementById('attachmentPreviews');
                        if (previewsContainer) {
                            previewsContainer.innerHTML = '';
                        }
                        
                        // Clear list
                        const attachmentsList = document.getElementById('attachmentsList');
                        if (attachmentsList) {
                            attachmentsList.innerHTML = '';
                        }
                    }
                });
            }
        });
    </script>
    @endpush

</x-app-layout>