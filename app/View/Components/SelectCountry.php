<?php

namespace App\View\Components;

use App\Models\Country;
use App\Models\Listing;
use App\Models\ListingStatus;
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
        $this->countries = Cache::remember(
            'countries-with-active-listings',
            now()->addHours(1),
            function () {
                // Get countries that have active listings
                return Country::whereHas('listings', function ($query) {
                    // Apply the active scope to filter for open, non-expired listings
                    $query->active();
                })
                    ->orderBy('name', 'asc')
                    ->get();
            }
        );
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