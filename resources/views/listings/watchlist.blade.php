<x-app-layout>
    <main>
        <!-- New Listings -->
        <section>
          <div class="container">
            <div class="flex justify-between items-center">
              <h2>My Listings Watchlist</h2>
              @if($listings->total() > 0)
                <div class="pagination-summary">
                  <p>
                    Showing {{ $listings->firstItem() }} to {{ $listings->lastItem() }}
                    of {{ $listings->total() }} results
                  </p>
                </div>
              @endif
            </div>
            <div class="listing-items-listing">
                @foreach ($listings as $listing)
                    <x-listing-item :listing="$listing" :isInWatchlist="true"/>
                @endforeach
            </div>

            @if($listings->count() === 0)
              <div class="text-center p-large">
                <p>You have no listings in your watchlist.</p>
              </div>
            @endif
  
            {{ $listings->onEachSide(3)->links()}}
          </div>
        </section>
        <!--/ New Listings -->
      </main>
  </x-app-layout>