<!-- Find a listing form -->
<section class="find-a-listing">
    <div class="container">
        <form
            action="{{ route('listings.search') }}"
            method="GET"
            class="find-a-listing-form card p-medium"
        >
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="form-group" style="margin-right: 10px;">
                        <label for="search_text" class="fw-bold mb-2">Search term</label>
                        <input type="text" 
                            id="search_text" 
                            name="search_text" 
                            class="form-control" 
                            placeholder="Search listings by title or description"
                            value="{{ request('search_text') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <x-select-manufacturer />
                </div>
            </div>
            
            <div class="row mb-3 my-small">
                <div class="col-md-8">
                    <x-select-product />
                </div>
                <div class="col-md-4">
                    <x-select-country />
                </div>
            </div>
            
            <div class="row flex justify-content-end my-small">
                <div class="col-12 text-right" style="width: 100%">
                    <button class="btn btn-primary btn-find-a-listing-submit">
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>