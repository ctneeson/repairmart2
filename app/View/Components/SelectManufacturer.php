<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use App\Models\Manufacturer;

class SelectManufacturer extends Component
{
    public Collection $manufacturers;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->manufacturers = Manufacturer::whereHas('listings')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-manufacturer', [
            'manufacturers' => $this->manufacturers,
        ]);
    }
}