@props(['name' => 'country_id', 'id' => 'country_id', 'value' => '', 'disabled' => false, 'required' => false])

@php
$isDisabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
@endphp

<select 
    name="{{ $name }}" 
    id="{{ $id }}" 
    class="form-select"
    {{ $isDisabled ? 'disabled' : '' }}
    {{ $required ? 'required' : '' }}>
    <option value="">Select a country</option>
    @foreach($countries as $country)
        <option value="{{ $country->id }}" {{ $value == $country->id ? 'selected' : '' }}>
            {{ $country->name }}
        </option>
    @endforeach
</select>