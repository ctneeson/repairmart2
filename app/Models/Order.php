<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'listing_id',
        'quote_id',
        'status_id',
        'override_quote',
        'override_amount',
    ];


    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function quote(): HasOne
    {
        return $this->hasOne(Quote::class, 'quote_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function customer(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class, // The final model we want to access
            Listing::class, // The intermediate model
            'id', // Foreign key on the listings table
            'id', // Foreign key on the users table
            'listing_id', // Local key on the orders table
            'user_id' // Local key on the listings table
        );
    }

    public function repairSpecialist(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class, // The final model we want to access
            Quote::class, // The intermediate model
            'id', // Foreign key on the quotes table
            'id', // Foreign key on the users table
            'quote_id', // Local key on the orders table
            'user_id' // Local key on the quotes table
        );
    }

    public function deliveryMethod(): HasOneThrough
    {
        return $this->hasOneThrough(
            DeliveryMethod::class, // The final model we want to access
            Quote::class, // The intermediate model
            'id', // Foreign key on the quotes table
            'id', // Foreign key on the users table
            'quote_id', // Local key on the orders table
            'deliverymethod_id' // Local key on the quotes table
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'order_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(OrderFeedback::class);
    }
}
