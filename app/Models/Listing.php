<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'listing_status_id',
        'manufacturer_id',
        'title',
        'detail',
        'budget_currency_id',
        'budget',
        'use_default_location',
        'override_address_line1',
        'override_address_line2',
        'override_city',
        'override_country_id',
        'override_postcode',
        'expiry',
    ];

    // public $timestamps = false;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ListingStatus::class, 'status_id');
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'budget_currency_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'override_country_id');
    }

    public function primaryAttachment(): HasOne
    {
        return $this->hasOne(Attachment::class, 'listing_id')
            ->oldestOfMany('position');
    }

    public function attachments(): HasMany
    {
        return $this->HasMany(Attachment::class, 'listing_id');
    }

    public function quotes(): HasMany
    {
        return $this->HasMany(Quote::class, 'listing_id');
    }

    public function quotesReceivedFrom(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class, // The final model we want to access
            Quote::class, // The intermediate model
            'listing_id', // Foreign key on the quotes table
            'user_id', // Foreign key on the users table
            'id', // Local key on the listings table
            'id' // Local key on the quotes table
        );
    }

    public function order(): HasOneThrough
    {
        return $this->hasOneThrough(
            Order::class, // The final model we want to access
            Quote::class, // The intermediate model
            'listing_id', // Foreign key on the quotes table
            'quote_id', // Foreign key on the orders table
            'id', // Local key on the listings table
            'id' // Local key on the quotes table
        );
    }

    public function orderAssignedTo(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class, // The final model we want to access
            Quote::class, // The intermediate model
            'listing_id', // Foreign key on the quotes table
            'user_id', // Foreign key on the users table
            'id', // Local key on the listings table
            'id' // Local key on the quotes table
        );
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class, // The related model
            'listings_products', // The pivot table
            'listing_id', // Foreign key on the pivot table for the current model
            'product_id' // Foreign key on the pivot table for the related model
        );
    }
}
