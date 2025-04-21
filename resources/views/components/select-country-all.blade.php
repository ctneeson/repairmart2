@props(['value' => '', 'id' => ''])

<select name="country_id" id="country_id" class="form-select">
    <option value="">Select a country</option>
    @foreach($countries as $country)
        <option value="{{ $country->id }}" {{ $value == $country->id ? 'selected' : '' }}>
            {{ $country->name }}
        </option>
    @endforeach
</select>