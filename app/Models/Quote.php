<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'listing_id',
        'quote_status_id',
        'quote_currency_id',
        'amount',
        'estimated_turnaround',
        'use_default_location',
        'override_address_line1',
        'override_address_line2',
        'override_city',
        'override_country_id',
        'override_postcode',
        'expiry',
    ];

    public function customer(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class, // The final model we want to access
            Listing::class, // The intermediate model
            'id', // Foreign key on the listings table
            'id', // Foreign key on the users table
            'listing_id', // Local key on the quotes table
            'user_id' // Local key on the listings table
        );
    }

    public function repairSpecialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(QuoteStatus::class, 'quote_status_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'quote_currency_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'override_country_id');
    }

    public function deliveryMethod(): BelongsTo
    {
        return $this->belongsTo(DeliveryMethod::class, 'deliverymethod_id');
    }
}
