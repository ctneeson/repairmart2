<?php

namespace App\View\Components;

use App\Models\Country;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class SelectCountry extends Component
{
    public Collection $countries;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->countries = Country::whereHas('listings', function ($query) {
            $query->where('use_default_location', 0)
                ->orWhereHas('customer', function ($query) {
                    $query->where('use_default_location', 1);
                });
        })->orderBy('name', 'asc')->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-country', [
            'countries' => $this->countries,
        ]);
    }
}