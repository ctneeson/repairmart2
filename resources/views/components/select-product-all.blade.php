{{-- <select id="productSelect" name="product_id">
    <option value="">Product</option>
        @foreach ($products as $product)
        <option value="{{ $product->id }}">{{ $product->category }} > {{ $product->subcategory }}</option>
        @endforeach
    </select> --}}
    <select id="productSelect" name="product_select" class="form-control">
        <option value="">Select a product</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}" data-category="{{ $product->category }}" data-subcategory="{{ $product->subcategory }}" {{ in_array($product->id, $value) ? 'selected' : '' }}>
                {{ $product->category }} > {{ $product->subcategory }}
            </option>
        @endforeach
    </select>