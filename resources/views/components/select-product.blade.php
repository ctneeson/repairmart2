<div class="dropdown" style="width: 100%;">
    <label for="dropdownMenuButtonProduct" data-label="Products">Products ({{ count(request('product_ids', [])) }})</label>
    <div class="dropdown-toggle-wrapper">
        <button class="dropdown-toggle product-toggle" type="button" id="dropdownMenuButtonProduct" aria-expanded="false">
            Select Products
        </button>
        <button type="button" class="reset-button" aria-label="Reset">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="reset-icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M4 4a9 9 0 1 1-3 7.5M4 9h5" />
            </svg>
        </button>
    </div>
    <div class="dropdown-menu product-menu" aria-labelledby="dropdownMenuButtonProduct">
        <div class="search-listings-checkboxes">
            @foreach ($products as $product)
                <div class="checkbox-item">
                    <input type="checkbox" id="product_{{ $product->id }}" name="product_ids[]" value="{{ $product->id }}" {{ in_array($product->id, request('product_ids', [])) ? 'checked' : '' }}>
                    <label for="product_{{ $product->id }}">{{ $product->category }} > {{ $product->subcategory }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>