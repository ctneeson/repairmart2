<select id="manufacturerSelect" name="manufacturer_id">
    <option value="">Manufacturer</option>
        @foreach ($manufacturers as $manufacturer)
        <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
        @endforeach
    </select>