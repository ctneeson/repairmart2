<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category',
        'subcategory'
    ];

    public function listings(): BelongsToMany
    {
        return $this->belongsToMany(
            Listing::class, // The related model
            'listings_products', // The pivot table
            'product_id', // Foreign key on the pivot table for the current model
            'listing_id' // Foreign key on the pivot table for the related model
        );
    }
}
