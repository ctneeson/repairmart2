<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $listings = Cache::remember('home-listings', now()->addMinute(), function () {
            return Listing::where('published_at', '<=', now())
                ->with([
                    'country',
                    'customer',
                    'customer.country',
                    'manufacturer',
                    'currency',
                    'primaryAttachment',
                    'products',
                    'watchlistUsers'
                ])
                ->orderBy('published_at', 'desc')
                ->limit(30)
                ->get();
        });

        return View::make('home.index', ['listings' => $listings]);
    }
}
