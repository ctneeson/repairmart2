<div class="dropdown" style="width: 100%;">
    <label for="dropdownMenuButtonCountry" data-label="Country">Country ({{ count(request('country_ids', [])) }})</label>
    <div class="dropdown-toggle-wrapper">
        <button class="dropdown-toggle country-toggle" type="button" id="dropdownMenuButtonCountry" aria-expanded="false">
            Select Country
        </button>
        <button type="button" class="reset-button" aria-label="Reset">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="reset-icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M4 4a9 9 0 1 1-3 7.5M4 9h5" />
            </svg>
        </button>
    </div>
    <div class="dropdown-menu country-menu" aria-labelledby="dropdownMenuButtonCountry">
        <div class="search-listings-checkboxes">
            @foreach ($countries as $country)
                <div class="checkbox-item">
                    <input type="checkbox" id="country_{{ $country->id }}" name="country_ids[]" value="{{ $country->id }}" {{ in_array($country->id, request('country_ids', [])) ? 'checked' : '' }}>
                    <label for="country_{{ $country->id }}">{{ $country->name }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>