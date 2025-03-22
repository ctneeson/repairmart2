<x-app-layout>
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
                    <div class="form-group has-error">
                      <label>Title</label>
                      <input placeholder="Title" name="title" />
                      <p class="error-message">This field is required</p>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group has-error">
                      <label>Product/s (max. 3)</label>
                      <div id="product-select-container" style="position: relative;">
                        <a href="#" id="add-product-link" style="display: none; position: absolute; right: 0; top: -25px;">+</a>
                        <div class="product-select-wrapper">
                          <x-select-product-all />
                        </div>
                        <div id="selected-products"></div>
                      </div>
                      <p class="error-message">This field is required</p>
                    </div>
                  </div>
                </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group has-error">
                        <label>Manufacturer</label>
                        <x-select-manufacturer-all />
                        <p class="error-message">This field is required</p>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group has-error">
                        <label>Detailed Description</label>
                        <textarea rows="10" name="description" ></textarea>
                        <p class="error-message">This field is required</p>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label>Expiry (Days)</label>
                        <select name="expiry_days">
                          <option value="7">7 days</option>
                          <option value="14">14 days</option>
                          <option value="30" selected>30 days</option>
                          <option value="60">60 days</option>
                          <option value="90">90 days</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label>Budget Currency</label>
                        <x-select-currency-all />
                        <p class="error-message">This field is required</p>
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <label>Budget Amount</label>
                        <input type="number" placeholder="Budget" name="budget" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label>Use my default address</label>
                        <input type="hidden" name="use_default_address" value="0">
                        <input type="checkbox" id="use-default-address" name="use_default_address" value="1" checked>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label>Address Line 1</label>
                        <input id="address_line1" placeholder="Address Line 1" name="address_line1" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label>Address Line 2</label>
                        <input id="address_line2" placeholder="Address Line 2" name="address_line2" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label>Town/City</label>
                        <input id="city" placeholder="Town/City" name="city" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label>Postcode</label>
                        <input id="postcode" placeholder="Postcode" name="postcode" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label>Country</label>
                        <x-select-country-all id="country" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label>Phone</label>
                        <input id="phone" placeholder="Phone" name="phone" />
                      </div>
                    </div>
                  </div>
                <div class="form-group">
                    <label class="checkbox">
                      Publish on:
                    </label>
                    <input type="date" name="published_at" />
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
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" fill="none"/>
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 8v8m-4-4h8"
                        />
                      </svg>
                    </div>
                    <input id="listingFormAttachmentUpload" type="file" name="attachments[]" multiple accept="image/*,video/*" />
                  </div>
                  <div id="attachmentPreviews" class="listing-form-attachments"></div>
                  <div id="attachmentsList" style="margin-top: 20px;"></div> <!-- Added this line -->
                </div>
              </div>
              <div class="p-medium" style="width: 100%">
                <div class="flex justify-end gap-1">
                  <button type="button" class="btn btn-default">Reset</button>
                  <button class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>
          </div>
      </main>
  </x-app-layout>
  
  <script src="{{ asset('js/listings-create-dynamic-product-select.js') }}"></script>
  <script src="{{ asset('js/listings-create-toggle-address-inputs.js') }}"></script>
