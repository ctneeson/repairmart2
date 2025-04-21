<?php

namespace App\View\Components;

use App\Models\Country;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class SelectCountryAll extends Component
{
    public Collection $countries;
    public $value;

    /**
     * Create a new component instance.
     * 
     * @param mixed $value The pre-selected value (country ID)
     */
    public function __construct($value = null)
    {
        $this->value = $value;

        $this->countries = Cache::rememberForever('countries-all', function () {
            return Country::orderBy('name', 'asc')->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-country-all');
    }
}