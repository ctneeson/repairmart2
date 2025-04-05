<x-app-layout title="Edit Listing">
  <main>
    <div class="container-small">
      <h1 class="listing-details-page-title">Edit Listing</h1>
        <form
          action="{{ route('listings.update', $listing) }}"
          method="POST"
          enctype="multipart/form-data"
          class="card add-new-listing-form"
        >
        @csrf
        @method('PUT')
          <div class="form-content">
            <div class="form-details">
              <div class="row">
                  <div class="col">
                      <div class="form-group @error('title') has-error @enderror">
                          <label>Title</label>
                          <input placeholder="Title" name="title" value="{{ old('title', $listing->title) }}" />
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
                                        $selectedProductIds = old('product_ids', $listing->products->pluck('id')->toArray());
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
                          <x-select-manufacturer-all :value="old('manufacturer_id', $listing->manufacturer_id)" />
                          <p class="error-message">{{ $errors->first('manufacturer_id') }}</p>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col">
                      <div class="form-group @error('description') has-error @enderror">
                          <label>Detailed Description</label>
                          <textarea rows="10" name="description">{{ old('description', $listing->description) }}</textarea>
                          <p class="error-message">{{ $errors->first('description') }}</p>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col">
                      <div class="form-group @error('expiry_days') has-error @enderror">
                          <label>Expiry (Days)</label>
                          <select name="expiry_days">
                              <option value="7" {{ old('expiry_days', $listing->expiry_days) == '7' ? 'selected' : '' }}>7 days</option>
                              <option value="14" {{ old('expiry_days', $listing->expiry_days) == '14' ? 'selected' : '' }}>14 days</option>
                              <option value="30" {{ old('expiry_days', $listing->expiry_days) == '30' ? 'selected' : '' }}>30 days</option>
                              <option value="60" {{ old('expiry_days', $listing->expiry_days) == '60' ? 'selected' : '' }}>60 days</option>
                              <option value="90" {{ old('expiry_days', $listing->expiry_days) == '90' ? 'selected' : '' }}>90 days</option>
                          </select>
                          <p class="error-message">{{ $errors->first('expiry_days') }}</p>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col">
                      <div class="form-group @error('currency_id') has-error @enderror">
                          <label>Budget Currency</label>
                          <x-select-currency-all :value="old('currency_id', $listing->currency_id)" />
                          <p class="error-message">{{ $errors->first('currency_id') }}</p>
                      </div>
                  </div>
                  <div class="col">
                      <div class="form-group @error('budget') has-error @enderror">
                          <label>Budget Amount</label>
                          <input type="number" placeholder="Budget" name="budget"  value="{{ old('budget', $listing->budget) }}" />
                          <p class="error-message">{{ $errors->first('budget') }}</p>
                      </div>
                  </div>
              </div>
              <div class="row">
                <div class="form-group">
                  <label>Use my default address</label>
                  <!-- Hidden field will always be submitted -->
                  <input type="hidden" name="use_default_location" value="0">
                  <!-- Checkbox will override the hidden field only when checked -->
                  <input type="checkbox" id="use-default-location" name="use_default_location" value="1" 
                         {{ old('use_default_location', $listing->use_default_location) ? 'checked' : '' }} />
                </div>
              </div>
              <div id="address-fields">
                <div class="row">
                    <div class="col">
                        <div class="form-group @error('address_line1') has-error @enderror">
                            <label>Address Line 1</label>
                            <input id="address_line1" 
                                  placeholder="Address Line 1" 
                                  name="address_line1" 
                                  value="{{ old('address_line1', $listing->address_line1) }}"
                                  data-original="{{ $listing->address_line1 }}"
                                  data-user="{{ auth()->user()->address_line1 }}" />
                            <p class="error-message">{{ $errors->first('address_line1') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group @error('address_line2') has-error @enderror">
                            <label>Address Line 2</label>
                            <input id="address_line2" 
                                  placeholder="Address Line 2" 
                                  name="address_line2" 
                                  value="{{ old('address_line2', $listing->address_line2) }}"
                                  data-original="{{ $listing->address_line2 }}"
                                  data-user="{{ auth()->user()->address_line2 }}" />
                            <p class="error-message">{{ $errors->first('address_line2') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group @error('city') has-error @enderror">
                            <label>Town/City</label>
                            <input id="city" 
                                  placeholder="Town/City" 
                                  name="city" 
                                  value="{{ old('city', $listing->city) }}"
                                  data-original="{{ $listing->city }}"
                                  data-user="{{ auth()->user()->city }}"/>
                            <p class="error-message">{{ $errors->first('city') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group @error('postcode') has-error @enderror">
                            <label>Postcode</label>
                            <input id="postcode" 
                                  placeholder="Postcode" 
                                  name="postcode" 
                                  value="{{ old('postcode', $listing->postcode) }}"
                                  data-original="{{ $listing->postcode }}"
                                  data-user="{{ auth()->user()->postcode }}" />
                            <p class="error-message">{{ $errors->first('postcode') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group @error('country_id') has-error @enderror">
                            <label>Country</label>
                            <x-select-country-all 
                                  id="countrySelect" 
                                  :value="old('country_id', $listing->country_id)" 
                                  data-original="{{ $listing->country_id }}"
                                  data-user="{{ auth()->user()->country_id }}" />
                            <p class="error-message">{{ $errors->first('country_id') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group @error('phone') has-error @enderror">
                            <label>Phone</label>
                            <input id="phone" 
                                  placeholder="Phone" 
                                  name="phone" 
                                  value="{{ old('phone', $listing->phone) }}"
                                  data-original="{{ $listing->phone }}"
                                  data-user="{{ auth()->user()->phone }}" />
                            <p class="error-message">{{ $errors->first('phone') }}</p>
                        </div>
                    </div>
                </div>
              </div>
              <div class="form-group @error('published_at') has-error @enderror">
                  <label class="checkbox">
                      Publish on:
                  </label>
                  <input type="date" name="published_at" value="{{ old('published_at', $listing->published_at) }}" />
                  <p class="error-message">{{ $errors->first('published_at') }}</p>
              </div>
          </div>
            <div class="form-attachments">
              <p>
                Manage attachments <a href="{{ route('listings.attachments', $listing) }}">here</a>
              </p>
              <div class="listing-form-attachments">
                @foreach ($listing->attachments as $attachment)
                <a href="#" class="listing-form-attachment-preview" data-type="{{ $attachment->mime_type }}">
                  @if(Str::startsWith($attachment->mime_type, 'image/'))
                    <img src="{{ $attachment->getUrl() }}" alt="" />
                  @elseif(Str::startsWith($attachment->mime_type, 'video/'))
                    <video src="{{ $attachment->getUrl() }}" muted></video>
                  @else
                    <div>Unknown type: {{ $attachment->mime_type }}</div>
                  @endif
                </a>
                @endforeach
              </div>
            </div>
          </div>
          <div class="p-medium" style="width: 100%">
            <div class="flex justify-end gap-1">
                <a href="{{ route('listings.show', $listing) }}" class="btn btn-default">Cancel</a>
                <button type="button" class="btn btn-default">Reset</button>
                <button class="btn btn-primary">Submit</button>
            </div>
          </div>
        </form>
    </div>
  </main>

  <div id="product-data" 
    data-products="{{ json_encode($listing->products->map(fn($p) => [
        'id' => $p->id,
        'category' => $p->category,
        'subcategory' => $p->subcategory
        ])) }}"
    style="display: none;"
    ></div>

    @vite([
        'resources/js/listings-edit.js',
        'resources/js/listings-create-dynamic-product-select.js',
        'resources/js/listings-edit-toggle-address-inputs.js',
      ])

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Reset button script loaded');
    
    const resetButton = document.querySelector('.btn.btn-default[type="button"]');
    if (!resetButton) return;
    
    resetButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (confirm('Are you sure you want to reset all changes?')) {
            // This uses the native form reset functionality and then triggers address toggle
            document.querySelector('form.add-new-listing-form').reset();
            
            // Trigger change event on use_default_location checkbox to update address fields visibility
            const useDefaultLocation = document.getElementById('use-default-location');
            if (useDefaultLocation) {
                const event = new Event('change', { bubbles: true });
                useDefaultLocation.dispatchEvent(event);
            }
            
            // Re-trigger product selection from original data
            if (typeof resetProductSelection === 'function') {
                resetProductSelection();
            }
        }
    });
});
</script>
@endpush

</x-app-layout>