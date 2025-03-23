@props(['value' => ''])

<select name="currency_id" {{ $attributes }}>
    <option value="">Select a currency</option>
    @foreach($currencies as $currency)
        <option value="{{ $currency->id }}" {{ $value == $currency->id ? 'selected' : '' }}>
            {{ $currency->iso_code }} - {{ $currency->name }}
        </option>
    @endforeach
</select>