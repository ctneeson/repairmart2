<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Listing;
use App\Models\Product;
use App\Models\CarType;
use App\Models\CarFeatures;
use App\Models\CarImage;
use App\Models\Maker;
use App\Models\Model as CarModel;
use App\Models\FuelType;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
class HomeController extends Controller
{
    public function index(Request $request)
    {
        $listings = Listing::where('published_at', '<=', now())
            ->with([
                'country',
                'customer',
                'customer.country',
                'manufacturer',
                'currency',
                'primaryAttachment',
                'products'
            ])
            ->orderBy('published_at', 'desc')
            ->limit(30)
            ->get();

        return View::make('home.index', ['listings' => $listings]);
    }
}
