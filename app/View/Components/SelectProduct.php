<?php

namespace App\View\Components;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class SelectProduct extends Component
{
    public Collection $products;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->products = Cache::remember('products', now()->addMinute(), function () {
            return Product::whereHas('listings')
                ->orderBy('category', 'asc')
                ->orderBy('subcategory', 'asc')
                ->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-product', [
            'products' => $this->products,
        ]);
    }
}