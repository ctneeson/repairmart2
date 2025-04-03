<div class="dropdown">
    <label for="dropdownMenuButton{{ $id }}">{{ $label }}</label>
    <div class="dropdown-toggle-wrapper">
        <button class="dropdown-toggle {{ $toggleClass }}" type="button" id="dropdownMenuButton{{ $id }}" aria-expanded="false">
            {{ $placeholder }}
        </button>
        <button type="button" class="reset-button" aria-label="Reset">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="reset-icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M4 4a9 9 0 1 1-3 7.5M4 9h5" />
            </svg>
        </button>
    </div>
    <div class="dropdown-menu {{ $menuClass }}" aria-labelledby="dropdownMenuButton{{ $id }}">
        <div class="search-listings-checkboxes">
            @foreach ($items as $item)
                <div class="checkbox-item">
                    <input type="checkbox" id="{{ $id }}_{{ $item->id }}" name="{{ $name }}[]" value="{{ $item->id }}">
                    <label for="{{ $id }}_{{ $item->id }}">{{ $item->name }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>