<x-app-layout title="Create Listing">
    <main>
        <div class="container-small">
          <h1 class="listing-details-page-title">Add new listing</h1>
            <form
                action="{{ route('listings.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="card add-new-listing-form"
            >
            @csrf
                <div class="form-content">
                    <div class="form-details">
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('title') has-error @enderror">
                                    <label>Title</label>
                                    <input placeholder="Title" name="title"
                                        value="{{ old('title', $relistData['title'] ?? '') }}" />
                                    <p class="error-message">{{ $errors->first('title') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('product_ids') has-error @enderror @error('product_ids.*') has-error @enderror">
                                    <label>Product/s (max. 3)</label>
                                        <div id="product-select-container" style="position: relative;">
                                            <a href="#" id="add-product-link" style="display: none; position: absolute; right: 0; top: -25px;">Add</a>
                                            <div class="product-select-wrapper">
                                                @php
                                                    $selectedProductIds = old('product_ids', $relistData['product_ids'] ?? []);
                                                    $selectedProductIds = is_array($selectedProductIds) ? array_unique($selectedProductIds) : [$selectedProductIds];
                                                    $selectedProductIds = array_filter($selectedProductIds); // Remove any empty values
                                                @endphp
                                                <x-select-product-all :value="$selectedProductIds" />
                                            </div>
                                            <div id="selected-products"></div>
                                            <div id="product-hidden-inputs">
                                                @foreach ($selectedProductIds as $productId)
                                                    <input type="hidden" name="product_ids[]" value="{{ $productId }}">
                                                @endforeach
                                            </div>
                                        </div>
                                    <p class="error-message">{{ $errors->first('product_ids') }}</p>
                                    <p class="error-message">{{ $errors->first('product_ids.*') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('manufacturer_id') has-error @enderror">
                                    <label>Manufacturer</label>
                                    <x-select-manufacturer-all :value="old('manufacturer_id', $relistData['manufacturer_id'] ?? '')" />
                                    <p class="error-message">{{ $errors->first('manufacturer_id') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('description') has-error @enderror">
                                    <label>Detailed Description</label>
                                    <textarea rows="10" name="description">
                                        {{ old('description', $relistData['description'] ?? '') }}
                                    </textarea>
                                    <p class="error-message">{{ $errors->first('description') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('expiry_days') has-error @enderror">
                                    <label>Expiry (Days)</label>
                                    <select name="expiry_days">
                                        <option value="7" {{ old('expiry_days', $relistData['expiry_days'] ?? '') == '7' ? 'selected' : '' }}>7 days</option>
                                        <option value="14" {{ old('expiry_days', $relistData['expiry_days'] ?? '') == '14' ? 'selected' : '' }}>14 days</option>
                                        <option value="30" {{ old('expiry_days', $relistData['expiry_days'] ?? '') == '30' ? 'selected' : '' }}>30 days</option>
                                        <option value="60" {{ old('expiry_days', $relistData['expiry_days'] ?? '') == '60' ? 'selected' : '' }}>60 days</option>
                                        <option value="90" {{ old('expiry_days', $relistData['expiry_days'] ?? '') == '90' ? 'selected' : '' }}>90 days</option>
                                    </select>
                                    <p class="error-message">{{ $errors->first('expiry_days') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('currency_id') has-error @enderror">
                                    <label>Budget Currency</label>
                                    <x-select-currency-all :value="old('currency_id', $relistData['currency_id'] ?? '')" />
                                    <p class="error-message">{{ $errors->first('currency_id') }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group @error('budget') has-error @enderror">
                                    <label>Budget Amount</label>
                                    <input type="number" placeholder="Budget" name="budget"
                                        value="{{ old('budget', $relistData['budget'] ?? '') }}" />
                                    <p class="error-message">{{ $errors->first('budget') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Use my default address</label>
                                    <input type="hidden" name="use_default_location" value="0">
                                    <input type="checkbox" id="use-default-location"
                                        name="use_default_location" value="1"
                                        {{ old('use_default_location', $relistData['use_default_location'] ?? 1) ? 'checked' : '' }} />
                                </div>
                            </div>
                        </div>
                        <div id="address-fields">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('address_line1') has-error @enderror">
                                        <label>Address Line 1</label>
                                        <input id="address_line1" placeholder="Address Line 1" name="address_line1"
                                            value="{{ old('address_line1', $relistData['address_line1'] ?? auth()->user()->address_line1) }}" />
                                        <p class="error-message">{{ $errors->first('address_line1') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('address_line2') has-error @enderror">
                                        <label>Address Line 2</label>
                                        <input id="address_line2" placeholder="Address Line 2" name="address_line2"
                                            value="{{ old('address_line2', $relistData['address_line2'] ?? auth()->user()->address_line2) }}" />
                                        <p class="error-message">{{ $errors->first('address_line2') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('city') has-error @enderror">
                                        <label>Town/City</label>
                                        <input id="city" placeholder="Town/City" name="city"
                                            value="{{ old('city', $relistData['city'] ?? auth()->user()->city) }}"/>
                                        <p class="error-message">{{ $errors->first('city') }}</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group @error('postcode') has-error @enderror">
                                        <label>Postcode</label>
                                        <input id="postcode" placeholder="Postcode" name="postcode"
                                            value="{{ old('postcode', $relistData['postcode'] ?? auth()->user()->postcode) }}" />
                                        <p class="error-message">{{ $errors->first('postcode') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('country_id') has-error @enderror">
                                        <label>Country</label>
                                        <x-select-country-all id="countrySelect" :value="old('country_id', $relistData['country_id'] ?? auth()->user()->country_id)" />
                                        <p class="error-message">{{ $errors->first('country_id') }}</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group @error('phone') has-error @enderror">
                                        <label>Phone</label>
                                        <input id="phone" placeholder="Phone" name="phone"
                                            value="{{ old('phone', $relistData['phone'] ?? auth()->user()->phone) }}" />
                                        <p class="error-message">{{ $errors->first('phone') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group @error('published_at') has-error @enderror">
                            <label class="checkbox">
                                Publish on:
                            </label>
                            <input 
                                type="date" 
                                name="published_at" 
                                id="published_at"
                                min="{{ now()->format('Y-m-d') }}" 
                                value="{{ old('published_at', now()->format('Y-m-d')) }}" 
                            />
                            <p class="error-message">{{ $errors->first('published_at') }}</p>
                        </div>
                    </div>
                    <div class="form-attachments">
                        <!-- Add this section for existing attachments from relisted listing -->
                        @if(!empty($attachments) && $attachments->count() > 0)
                            <div class="mb-4">
                                <h3>Attachments from Original Listing</h3>
                                <p class="info-message">
                                    <small>Select which attachments you'd like to include in your new listing.</small>
                                </p>
                                
                                <div id="relistAttachments" class="listing-form-attachments">
                                    @foreach($attachments as $attachment)
                                        <div class="attachment-preview">
                                            <div class="attachment-preview-inner">
                                                @if(Str::startsWith($attachment->mime_type, 'image/'))
                                                    <img src="{{ Storage::url($attachment->path) }}" alt="Attachment">
                                                @elseif(Str::startsWith($attachment->mime_type, 'video/'))
                                                    <video controls>
                                                        <source src="{{ Storage::url($attachment->path) }}" type="{{ $attachment->mime_type }}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @else
                                                    <div class="file-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="attachment-checkbox">
                                                <input type="checkbox" name="duplicate_attachments[]" 
                                                    value="{{ $attachment->id }}" id="attachment-{{ $attachment->id }}" checked>
                                                <label for="attachment-{{ $attachment->id }}">
                                                    {{ Str::limit(basename($attachment->path), 20) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="divider my-4"></div>
                            <h3>Add New Attachments</h3>
                        @endif
                        
                        <!-- Your existing upload section -->
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
                <div class="p-medium" style="width: 100%">
                    <div class="flex justify-end gap-1">
                        <button type="button" class="btn btn-default">Cancel</button>
                        <button type="button" class="btn btn-default">Reset</button>
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <style>
        #relistAttachments {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        
        #relistAttachments .attachment-preview {
            width: 150px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        #relistAttachments .attachment-preview-inner {
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8fafc;
            overflow: hidden;
        }
        
        #relistAttachments .attachment-preview-inner img,
        #relistAttachments .attachment-preview-inner video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        #relistAttachments .file-icon {
            width: 40px;
            height: 40px;
            color: #64748b;
        }
        
        #relistAttachments .attachment-checkbox {
            padding: 8px;
            background-color: #fff;
            border-top: 1px solid #e2e8f0;
        }
        
        #relistAttachments .attachment-checkbox label {
            font-size: 0.75rem;
            margin-left: 5px;
            color: #4b5563;
            cursor: pointer;
        }
        
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            width: 100%;
        }
        
        .my-4 {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        #selected-products {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        #selected-products .selected-product-tag {
            background-color: #e9ecef;
            border-radius: 4px;
            padding: 5px 10px;
            display: inline-flex;
            align-items: center;
            font-size: 14px;
        }

        #selected-products .remove-product {
            margin-left: 8px;
            cursor: pointer;
            color: #dc3545;
        }
    </style>

    @include('js.listings-create-toggle-address-inputs')

    @vite([
        'resources/js/listings-create-dynamic-product-select.js',
        'resources/js/listings-create-reset-form.js',
    ])

</x-app-layout>