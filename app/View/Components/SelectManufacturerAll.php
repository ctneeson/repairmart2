<?php

namespace App\View\Components;

use App\Models\Manufacturer;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class SelectManufacturerAll extends Component
{
    public $value;
    public ?Collection $manufacturers;
    /**
     * Create a new component instance.
     */
    public function __construct($value = null)
    {
        $this->value = $value;

        $this->manufacturers = Cache::rememberForever('manufacturers-all', function () {
            return Manufacturer::orderBy('name', 'asc')->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-manufacturer-all');
    }
}
