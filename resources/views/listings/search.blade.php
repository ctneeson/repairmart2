<x-app-layout title="Search Listings">
    <main>
        <!-- Found Listings -->
        <section>
          <div class="container">
            <div class="sm:flex items-center justify-between mb-medium">
              <div class="flex items-center">
                <button class="show-filters-button flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" style="width: 20px">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6 13.5V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m12-3V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m-6-9V3.75m0 3.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 9.75V10.5" />
                  </svg>
                  Filters
                </button>
                <h2>Search Criteria</h2>
              </div>
    
              <select class="sort-dropdown">
                <option value="">Order By</option>
                <option value="-published_at" {{ request('sort') == '-published_at' ? 'selected' : '' }}>Published (desc.)</option>
                <option value="published_at" {{ request('sort') == 'published_at' ? 'selected' : '' }}>Published (asc.)</option>
                <option value="expiry_asc" {{ request('sort') == 'expiry_asc' ? 'selected' : '' }}>Expiry Date (asc.)</option>
                <option value="expiry_desc" {{ request('sort') == 'expiry_desc' ? 'selected' : '' }}>Expiry Date (desc.)</option>
              </select>
            </div>
            <div class="search-listing-results-wrapper">
              <div class="search-listings-sidebar">
                <div class="card card-found-listings">
                  <p class="m-0">Found <strong>{{ $listings->total() }}</strong> {{ Str::plural('listing', $listings->total()) }}</p>
    
                  <button class="close-filters-button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 24px">
                      <path fill-rule="evenodd"
                        d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                        clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
    
                <section class="find-a-listing">
                  <form action="" method="GET" class="find-a-listing-form card flex p-medium">
                    <div class="find-a-listing-inputs">
                      <div class="form-group">
                        <label for="search_text">Search term</label>
                        <input type="text" 
                            id="search_text" 
                            name="search_text" 
                            class="form-control" 
                            placeholder="Search listings by title or description"
                            value="{{ request('search_text') }}">
                      </div>
                      <div class="form-group">
                        <x-select-manufacturer />
                      </div>
                      <div class="form-group">
                        <x-select-product />
                      </div>
                      <div class="form-group">
                        <x-select-country />
                      </div>
                    </div>
                    <div class="flex my-medium justify-center">
                      <button class="btn btn-primary btn-find-a-listing-submit" style="width: 50%">
                        Search
                      </button>
                    </div>
                  </form>
                </section>
                <!--/ Find a listing form -->
              </div>
    
              <div class="search-listings-results">
                @if($listings->count())
                <div class="listing-items-listing">
                  @foreach($listings as $listing)
                    <x-listing-item :$listing :isInWatchlist="$listing->watchlistUsers->contains(Auth::user())"/>
                  @endforeach
                </div>
                @else
                <div class="text-center p-large">
                  No listings found for the given search criteria.
                </div>
                @endif
                {{ $listings->onEachSide(3)->links() }}
                </nav>
              </div>
            </div>
          </div>
        </section>
        <!--/ Found Listings -->
    </main>
</x-app-layout>
<script src="{{ asset('js/listings-search-dropdown.js') }}"></script>
