<select id="currencySelect" name="currency_id">
    <option value="">Currency</option>
        @foreach ($currencies as $currency)
        <option value="{{ $currency->id }}">{{ $currency->iso_code }} - {{ $currency->name }}</option>
        @endforeach
    </select>