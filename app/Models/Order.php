<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'quote_id',
        'customer_id',
        'specialist_id',
        'status_id',
        'override_quote',
        'amount',
        'customer_feedback_id',
        'customer_feedback',
        'specialist_feedback_id',
        'specialist_feedback',
    ];


    /**
     * Get the listing associated with this order through the quote.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function listing(): HasOneThrough
    {
        return $this->hasOneThrough(
            Listing::class,     // The final model we want to access
            Quote::class,       // The intermediate model
            'id',               // Foreign key on the quotes table
            'id',               // Foreign key on the listings table
            'quote_id',         // Local key on the orders table
            'listing_id'        // Local key on the quotes table
        );
    }

    /**
     * Get the quote associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    /**
     * Get the status associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    /**
     * Check if the order has a specific status.
     *
     * @param string $statusName
     * @return bool
     */
    public function hasStatus($statusName)
    {
        return $this->status->name === $statusName;
    }

    /**
     * Get the customer associated with this order.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the specialist associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function repairSpecialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    /**
     * Get the currency associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function currency(): HasOneThrough
    {
        return $this->hasOneThrough(
            Currency::class, // The final model we want to access
            Quote::class, // The intermediate model
            'id', // Foreign key on the quotes table
            'id', // Foreign key on the currencies table
            'quote_id', // Local key on the orders table
            'currency_id' // Local key on the quotes table
        );
    }

    /**
     * Get the delivery method associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
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

    /**
     * Get the attachments associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'order_id');
    }

    /**
     * Get the comments associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(OrderComment::class, 'order_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the emails associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails(): HasMany
    {
        return $this->HasMany(Email::class, 'order_id');
    }

    /**
     * Get the customer feedback type associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customerFeedbackType(): BelongsTo
    {
        return $this->belongsTo(FeedbackType::class, 'customer_feedback_id');
    }

    /**
     * Get the specialist feedback type associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialistFeedbackType(): BelongsTo
    {
        return $this->belongsTo(FeedbackType::class, 'specialist_feedback_id');
    }

    /**
     * Check if the order amount can be edited in the current status.
     *
     * @return bool
     */
    public function isAmountEditable(): bool
    {
        // Load the status relationship if it's not already loaded
        if (!$this->relationLoaded('status')) {
            $this->load('status');
        }

        return (bool) $this->status->amount_editable;
    }
}
