{{-- <select id="productSelect" name="product_ids[]" class="form-control" multiple size="5">
    <option value="">Select a product</option>
    @foreach ($products as $product)
        <option value="{{ $product->id }}" 
                data-category="{{ $product->category }}" 
                data-subcategory="{{ $product->subcategory }}" 
                {{ in_array($product->id, $value) ? 'selected' : '' }}>
            {{ $product->category }} > {{ $product->subcategory }}
        </option>
    @endforeach
</select> --}}

{{-- <select id="productSelect" name="product_ids[]" class="form-control" multiple size="8">
    <option value="">Select a product</option>
    @php $currentCategory = ''; @endphp
    
    @foreach ($products as $product)
        @if ($product->category !== $currentCategory)
            @if ($currentCategory !== '') 
                </optgroup>
            @endif
            <optgroup label="{{ $product->category }}">
            @php $currentCategory = $product->category; @endphp
        @endif
        
        <option value="{{ $product->id }}" 
                data-category="{{ $product->category }}" 
                data-subcategory="{{ $product->subcategory }}" 
                {{ in_array($product->id, $value) ? 'selected' : '' }}>
            {{ $product->subcategory }}
        </option>
    @endforeach
    
    @if ($currentCategory !== '') 
        </optgroup>
    @endif
</select> --}}

<select id="productSelect" name="product_select" class="form-control">
    <option value="">Select a product</option>
    @foreach ($products as $product)
        <option value="{{ $product->id }}" 
                data-category="{{ $product->category }}" 
                data-subcategory="{{ $product->subcategory }}">
            {{ $product->category }} > {{ $product->subcategory }}
        </option>
    @endforeach
</select>
