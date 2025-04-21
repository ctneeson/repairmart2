<?php

namespace App\View\Components;

use App\Models\Currency;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class SelectCurrencyAll extends Component
{
    public $value;
    public Collection $currencies;
    /**
     * Create a new component instance.
     */
    public function __construct($value = null)
    {
        $this->value = $value;

        $this->currencies = Cache::rememberForever('currencies-all', function () {
            return Currency::orderBy('iso_code', 'asc')->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-currency-all');
    }
}
