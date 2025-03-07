<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'override_country_id',
        'override_postcode',
        'expiry',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function quoteStatus(): BelongsTo
    {
        return $this->belongsTo(QuoteStatus::class, 'quote_status_id');
    }

    public function quoteCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'quote_currency_id');
    }

    public function quoteCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'override_country_id');
    }
}
