<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'override_country_id',
        'override_postcode',
        'expiry',
    ];


    // public $timestamps = false;

    public function listingStatus(): BelongsTo
    {
        return $this->belongsTo(ListingStatus::class, 'listing_status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'budget_currency_id');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ListingImage::class, 'listing_id')
            ->oldestOfMany('position');
    }

    public function images(): HasMany
    {
        return $this->HasMany(ListingImage::class, 'listing_id');
    }
}
