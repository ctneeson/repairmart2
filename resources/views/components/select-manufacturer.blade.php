<div class="dropdown" style="width: 100%;">
    <label for="dropdownMenuButtonManufacturer" data-label="Manufacturer">Manufacturer ({{ count(request('manufacturer_ids', [])) }})</label>
    <div class="dropdown-toggle-wrapper">
        <button class="dropdown-toggle manufacturer-toggle" type="button" id="dropdownMenuButtonManufacturer" aria-expanded="false">
            Select Manufacturer
        </button>
        <button type="button" class="reset-button" aria-label="Reset">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="reset-icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M4 4a9 9 0 1 1-3 7.5M4 9h5" />
            </svg>
        </button>
    </div>
    <div class="dropdown-menu manufacturer-menu" aria-labelledby="dropdownMenuButtonManufacturer">
        <div class="search-listings-checkboxes">
            @foreach ($manufacturers as $manufacturer)
                <div class="checkbox-item">
                    <input type="checkbox" id="manufacturer_{{ $manufacturer->id }}" name="manufacturer_ids[]" value="{{ $manufacturer->id }}" {{ in_array($manufacturer->id, request('manufacturer_ids', [])) ? 'checked' : '' }}>
                    <label for="manufacturer_{{ $manufacturer->id }}">{{ $manufacturer->name }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>