<select name="manufacturer_id" class="form-control">
    <option value="">Select a manufacturer</option>
    @foreach ($manufacturers as $manufacturer)
        <option value="{{ $manufacturer->id }}" {{ $manufacturer->id == $value ? 'selected' : '' }}>
            {{ $manufacturer->name }}
        </option>
    @endforeach
</select>