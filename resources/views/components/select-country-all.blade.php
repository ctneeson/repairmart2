<select id="countrySelect" name="country_id">
<option value="">Country</option>
    @foreach ($countries as $country)
    <option value="{{ $country->id }}">{{ $country->name }}</option>
    @endforeach
</select>