<?php

namespace App\View\Components;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class SelectProductAll extends Component
{
    public Collection $products;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->products = Product::orderBy('category', 'asc')
            ->orderBy('subcategory', 'asc')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-product-all');
    }
}
