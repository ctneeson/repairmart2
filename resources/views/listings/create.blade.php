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
                                    <input placeholder="Title" name="title" value="{{ old('title') }}" />
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
                                                    $selectedProductIds = old('product_ids', []);
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
                                    <x-select-manufacturer-all :value="old('manufacturer_id')" />
                                    <p class="error-message">{{ $errors->first('manufacturer_id') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('description') has-error @enderror">
                                    <label>Detailed Description</label>
                                    <textarea rows="10" name="description">{{ old('description') }}</textarea>
                                    <p class="error-message">{{ $errors->first('description') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('expiry_days') has-error @enderror">
                                    <label>Expiry (Days)</label>
                                    <select name="expiry_days">
                                        <option value="7" {{ old('expiry_days') == '7' ? 'selected' : '' }}>7 days</option>
                                        <option value="14" {{ old('expiry_days') == '14' ? 'selected' : '' }}>14 days</option>
                                        <option value="30" {{ old('expiry_days') == '30' ? 'selected' : '' }}>30 days</option>
                                        <option value="60" {{ old('expiry_days') == '60' ? 'selected' : '' }}>60 days</option>
                                        <option value="90" {{ old('expiry_days') == '90' ? 'selected' : '' }}>90 days</option>
                                    </select>
                                    <p class="error-message">{{ $errors->first('expiry_days') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group @error('currency_id') has-error @enderror">
                                    <label>Budget Currency</label>
                                    <x-select-currency-all :value="old('currency_id')" />
                                    <p class="error-message">{{ $errors->first('currency_id') }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group @error('budget') has-error @enderror">
                                    <label>Budget Amount</label>
                                    <input type="number" placeholder="Budget" name="budget"  value="{{ old('budget') }}" />
                                    <p class="error-message">{{ $errors->first('budget') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Use my default address</label>
                                    <input type="hidden" name="use_default_location" value="0">
                                    <input type="checkbox" id="use-default-location" name="use_default_location" value="1" 
                                        {{ old('use_default_location', 1) ? 'checked' : '' }} />
                                </div>
                            </div>
                        </div>
                        <div id="address-fields">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('address_line1') has-error @enderror">
                                        <label>Address Line 1</label>
                                        <input id="address_line1" placeholder="Address Line 1" name="address_line1" 
                                            value="{{ old('address_line1', auth()->user()->address_line1) }}" />
                                        <p class="error-message">{{ $errors->first('address_line1') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('address_line2') has-error @enderror">
                                        <label>Address Line 2</label>
                                        <input id="address_line2" placeholder="Address Line 2" name="address_line2" 
                                            value="{{ old('address_line2', auth()->user()->address_line2) }}" />
                                        <p class="error-message">{{ $errors->first('address_line2') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('city') has-error @enderror">
                                        <label>Town/City</label>
                                        <input id="city" placeholder="Town/City" name="city" 
                                            value="{{ old('city', auth()->user()->city) }}"/>
                                        <p class="error-message">{{ $errors->first('city') }}</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group @error('postcode') has-error @enderror">
                                        <label>Postcode</label>
                                        <input id="postcode" placeholder="Postcode" name="postcode" 
                                            value="{{ old('postcode', auth()->user()->postcode) }}" />
                                        <p class="error-message">{{ $errors->first('postcode') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('country_id') has-error @enderror">
                                        <label>Country</label>
                                        <x-select-country-all id="countrySelect" :value="old('country_id', auth()->user()->country_id)" />
                                        <p class="error-message">{{ $errors->first('country_id') }}</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group @error('phone') has-error @enderror">
                                        <label>Phone</label>
                                        <input id="phone" placeholder="Phone" name="phone" 
                                            value="{{ old('phone', auth()->user()->phone) }}" />
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

    @include('js.listings-create-toggle-address-inputs')

    @vite([
        'resources/js/listings-create-dynamic-product-select.js',
        'resources/js/listings-create-reset-form.js',
    ])

</x-app-layout>