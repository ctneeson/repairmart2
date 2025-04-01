<x-app-layout title='Home'>
  <!-- Home Slider -->
  <section class="hero-slider">
      <!-- Carousel wrapper -->
      <div class="hero-slides">
        <!-- Item 1 -->
        <div class="hero-slide">
          <div class="flex container">
            <div class="slide-content">
              <h2 class="hero-slider-title">
                <strong>For Customers</strong></br>
                Repair your electronics
              </h2>
              <div class="hero-slider-content">
                <p>
                  Avoid e-waste! Submit a repair request here for your electronics:
                  receive quotes from repair specialists and extend your product's life.
                </p>

                <button class="btn btn-hero-slider" href="/listings/create">Create a listing</button>
              </div>
            </div>
            <div class="slide-image">
              <img src="/img/electronics-repair.png" alt="" class="img-responsive" />
            </div>
          </div>
        </div>
        <!-- Item 2 -->
        <div class="hero-slide">
          <div class="container">
            <div class="slide-content">
              <h1 class="hero-slider-title">
                <strong>For Repair Specialists</strong> <br />
                Search electronics repair requests
              </h1>
              <div class="hero-slider-content">
                <p>
                  Find repair requests from customers based on
                  multiple search criteria: Product Type, Manufacturer, Location, etc...
                </p>

                <button class="btn btn-hero-slider" href="/listings/search">Find requests</button>
              </div>
            </div>
            <div class="slide-image">
              <img src="/img/electronics-repair.png" alt="" class="img-responsive" />
            </div>
          </div>
        </div>
        <button type="button" class="hero-slide-prev">
          <svg
            style="width: 18px"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 6 10"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 1 1 5l4 4"
            />
          </svg>
          <span class="sr-only">Previous</span>
        </button>
        <button type="button" class="hero-slide-next">
          <svg
            style="width: 18px"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 6 10"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m1 9 4-4-4-4"
            />
          </svg>
          <span class="sr-only">Next</span>
        </button>
      </div>
  </section>
  <!--/ Home Slider -->

  <main>
    <x-search-form action="/search" method="GET" />
    <!-- New Listings -->
      <section>
        <div class="container">
          <h2>Latest Listings</h2>
          @if ($listings->count()>0)
          <div class="listing-items-listing">
            @foreach($listings as $listing)
              <x-listing-item :listing="$listing" :isInWatchlist="$listing->watchlistUsers->contains(Auth::user())"/>
            @endforeach
            </div>
          </div>
          @else
          <div class="text-center p-large">
            <p>There are no listings published yet.</p>
          </div>
          @endif
        </div>
      </section>
      <!--/ New Listings -->
  </main>
</x-app-layout>

