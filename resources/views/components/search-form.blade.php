<!-- Find a listing form -->
<section class="find-a-listing">
    <div class="container">
        <form
        action="{{ route('listings.search') }}"
        method="GET"
        class="find-a-listing-form card flex p-medium"
        >
            <div class="find-a-listing-inputs"  style="width: 100%; align-items: center">
                <div style="width: 90%; max-width: 100%;">
                    <x-select-manufacturer />
                </div>
                <div style="width: 90%; max-width: 100%;">
                    <x-select-product />
                </div>
                <div style="width: 90%; max-width: 100%;">
                    <x-select-country />
                </div>
            </div>
            <div  style="width: 15%; display: flex; align-items: flex-end">
                <button class="btn btn-primary btn-find-a-listing-submit">
                Search
                </button>
            </div>
        </form>
    </div>
</section>
