@props(['value' => '', 'id' => ''])

<select name="country_id" id="{{ $id }}" {{ $attributes }}>
    <option value="">Select a country</option>
    @foreach($countries as $country)
        <option value="{{ $country->id }}" {{ $value == $country->id ? 'selected' : '' }}>
            {{ $country->name }}
        </option>
    @endforeach
</select>