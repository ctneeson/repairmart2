<x-app-layout title="Create Quote">
  <main>
      <div class="container-small">
          <h1 class="listing-details-page-title">Create Quote for Repair</h1>
          
          @if ($errors->any())
              <div class="alert alert-danger">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif

          <form action="{{ route('quotes.store') }}" method="POST" enctype="multipart/form-data" class="card p-large">
              @csrf
              
              <!-- Hidden Fields -->
              <input type="hidden" name="user_id" value="{{ auth()->id() }}">
              <input type="hidden" name="listing_id" value="{{ $listing->id }}">
              <input type="hidden" name="status_id" value="1"> <!-- Default status: Open -->
              
              <!-- Original Listing Information Section -->
              <div class="form-section mb-large">
                  <h2 class="mb-small">Original Listing</h2>
                  <div class="listing-summary card p-medium mb-medium">
                      <div class="d-flex justify-content-between align-items-top">
                          <div>
                              <h3>{{ $listing->title }}</h3>
                              <p>{{ \Illuminate\Support\Str::limit($listing->description, 150) }}</p>
                              
                              <div class="listing-meta">
                                  <span class="badge bg-info">{{ $listing->manufacturer->name }}</span>
                                  @foreach($listing->products as $product)
                                      <span class="badge bg-secondary">{{ $product->category }} > {{ $product->subcategory }}</span>
                                  @endforeach
                              </div>
                          </div>
                          <a href="{{ route('listings.show', $listing->id) }}" class="btn btn-outline-secondary btn-sm">View Listing</a>
                      </div>
                  </div>
              </div>
              
              <div class="form-content">
                  <!-- Quote Details Section -->
                  <div class="form-section mb-large">
                      <h2 class="mb-small">Quote Details</h2>
                      
                      <div class="form-details">
                          <div class="row">
                              <!-- Amount -->
                              <div class="col">
                                  <div class="form-group @error('amount') has-error @enderror">
                                      <label for="amount">Quote Amount</label>
                                      <div class="input-group">
                                          <x-select-currency-all name="currency_id" value="{{ old('currency_id') }}" />
                                          <input type="number" 
                                              name="amount" 
                                              id="amount" 
                                              class="form-control" 
                                              value="{{ old('amount') }}" 
                                              step="0.01" 
                                              min="0.01" 
                                              required>
                                      </div>
                                      @error('amount')
                                          <p class="error-message">{{ $message }}</p>
                                      @enderror
                                  </div>
                              </div>
                              
                              <!-- Turnaround Time -->
                              <div class="col">
                                  <div class="form-group @error('turnaround') has-error @enderror">
                                      <label for="turnaround">Turnaround Time (Days)</label>
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
                          
                          <!-- Delivery Method -->
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
                          
                          <!-- Quote Details -->
                          <div class="form-group @error('details') has-error @enderror">
                              <label for="details">Quote Details</label>
                              <textarea
                                  name="details"
                                  id="details"
                                  rows="5"
                                  placeholder="Describe your repair service, approach, and any other information the customer should know">{{ old('details') }}</textarea>
                              @error('details')
                                  <p class="error-message">{{ $message }}</p>
                              @enderror
                          </div>
                          
                          <!-- Location Information -->
                          <div class="form-group">
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
                          
                          <!-- Address Fields -->
                          <div id="address-fields" class="{{ old('use_default_location', '1') == '1' ? 'opacity-50' : '' }}">
                              <div class="row">
                                  <div class="col">
                                      <div class="form-group @error('address_line1') has-error @enderror">
                                          <label for="address_line1">Address Line 1</label>
                                          <input
                                              type="text"
                                              name="address_line1"
                                              id="address_line1"
                                              value="{{ old('address_line1', $user->address_line1) }}"
                                              {{ old('use_default_location', '1') == '1' ? 'readonly' : '' }}
                                              required
                                          >
                                          @error('address_line1')
                                              <p class="error-message">{{ $message }}</p>
                                          @enderror
                                      </div>
                                  </div>
                                  <div class="col">
                                      <div class="form-group @error('address_line2') has-error @enderror">
                                          <label for="address_line2">Address Line 2</label>
                                          <input
                                              type="text"
                                              name="address_line2"
                                              id="address_line2"
                                              value="{{ old('address_line2', $user->address_line2) }}"
                                              {{ old('use_default_location', '1') == '1' ? 'readonly' : '' }}
                                          >
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
                                              required
                                          >
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
                                              required
                                          >
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
                                          <x-select-country-all 
                                              name="country_id" 
                                              value="{{ old('country_id', $user->country_id) }}" 
                                              :disabled="old('use_default_location', '1') == '1'"
                                              required="true" 
                                          />
                                          @error('country_id')
                                              <p class="error-message">{{ $message }}</p>
                                          @enderror
                                      </div>
                                  </div>
                                  <div class="col">
                                      <div class="form-group @error('phone') has-error @enderror">
                                          <label for="phone">Phone</label>
                                          <input
                                              type="text"
                                              name="phone"
                                              id="phone"
                                              value="{{ old('phone', $user->phone) }}"
                                              {{ old('use_default_location', '1') == '1' ? 'readonly' : '' }}
                                              required
                                          >
                                          @error('phone')
                                              <p class="error-message">{{ $message }}</p>
                                          @enderror
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
                                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
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
                      <a href="{{ route('listings.show', $listing->id) }}" class="btn btn-default">Cancel</a>
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
              
              // Update each input's readonly status
              addressInputs.forEach(input => {
                  if (input.name !== 'country_id') {
                      input.readOnly = useDefault;
                  } else {
                      // For the country dropdown, we need to handle differently
                      if (useDefault) {
                          input.setAttribute('disabled', 'disabled');
                      } else {
                          input.removeAttribute('disabled');
                      }
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
                  document.getElementById('details').value = '';
                  
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