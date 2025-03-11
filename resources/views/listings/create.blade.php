<x-app-layout>
    <main>
        <div class="container-small">
          <h1 class="listing-details-page-title">Add new listing</h1>
          <form
            action=""
            method="POST"
            enctype="multipart/form-data"
            class="card add-new-listing-form"
          >
            <div class="form-content">
              <div class="form-details">
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Product</label>
                      <select>
                        <option value="">Product Category</option>
                        <option value="1">Audio-Visual > Audio Headphones & Accessories</option>
                        <option value="2">Audio-Visual > Blu-ray Players & Recorders</option>
                        <option value="3">Audio-Visual > Other-Misc.</option>
                      </select>
                      <p class="error-message">This field is required</p>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Manufacturer</label>
                      <select>
                        <option value="">Manufacturer</option>
                        <option value="1">3M</option>
                        <option value="2">Acer</option>
                        <option value="3">Aiwa</option>
                        <option value="4">Other</option>
                      </select>
                      <p class="error-message">This field is required</p>
                    </div>
                  </div>
                  <div class="col">

                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Title</label>
                      <input placeholder="Title" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Detailed Description</label>
                      <textarea rows="10"></textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Budget Currency</label>
                      <select>
                        <option value="">Budget Currency</option>
                        <option value="EUR">EUR</option>
                        <option value="GBP">GBP</option>
                        <option value="CHF">CHF</option>
                        <option value="PLN">PLN</option>
                        <option value="NOK">NOK</option>
                      </select>
                      <p class="error-message">This field is required</p>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label>Budget Amount</label>
                      <input type="number" placeholder="Amount" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Use my default address</label>
                      <input type="checkbox" checked/>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Address Line 1</label>
                      <input placeholder="Address Line 1" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Address Line 2</label>
                      <input placeholder="Address Line 2" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Town/City</label>
                      <input placeholder="Town/City" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Postcode</label>
                      <input placeholder="Postcode" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Country</label>
                      <select>
                        <option value="">Country</option>
                        <option value="GB">United Kingdom</option>
                        <option value="IE">Ireland</option>
                        <option value="FR">France</option>
                        <option value="NL">Netherlands</option>
                        <option value="BE">Belgium</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label>Phone</label>
                      <input placeholder="Phone" />
                    </div>
                  </div>
                </div>
              <div class="form-group">
                  <label class="checkbox">
                    <input type="checkbox" name="published" />
                    Published
                  </label>
                </div>
              </div>
              <div class="form-images">
                <div class="form-image-upload">
                  <div class="upload-placeholder">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      style="width: 48px"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                      />
                    </svg>
                  </div>
                  <input id="listingFormImageUpload" type="file" multiple />
                </div>
                <div id="imagePreviews" class="listing-form-images"></div>
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
