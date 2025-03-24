@props(['value' => ''])

<select name="manufacturer_id" class="form-control" {{ $attributes }}>
    <option value="">Select a manufacturer</option>
    @foreach($manufacturers as $manufacturer)
        <option value="{{ $manufacturer->id }}" {{ (string)$value === (string)$manufacturer->id ? 'selected' : '' }}>
            {{ $manufacturer->name }}
        </option>
    @endforeach
</select>