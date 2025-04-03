<?php

namespace App\View\Components;

use App\Models\Country;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class SelectCountry extends Component
{
    public Collection $countries;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->countries = Cache::remember('countries', now()->addMinute(), function () {
            return Country::whereHas('listings')
                ->orderBy('name', 'asc')
                ->get();
        });
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