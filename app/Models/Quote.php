<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use App\Models\Attachment;
use Carbon\Carbon;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'listing_id',
        'status_id',
        'currency_id',
        'deliverymethod_id',
        'amount',
        'turnaround',
        'description',
        'use_default_location',
        'address_line1',
        'address_line2',
        'city',
        'postcode',
        'country_id',
        'phone',
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
        return $this->belongsTo(QuoteStatus::class, 'status_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function deliveryMethod(): BelongsTo
    {
        return $this->belongsTo(DeliveryMethod::class, 'deliverymethod_id');
    }

    public function emails(): HasMany
    {
        return $this->HasMany(Email::class, 'quote_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'quote_id');
    }

    public function getUpdatedDate(): string
    {
        return (new Carbon($this->updated_at))->format('Y-m-d');
    }

}
