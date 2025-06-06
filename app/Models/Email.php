<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'subject',
        'content',
        'read_at'
    ];

    protected $dates = [
        'read_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class, // The related model
            'emails_recipients', // The pivot table
            'email_id', // Foreign key on the pivot table for the current model
            'recipient_id' // Foreign key on the pivot table for the related model
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'email_id')->orderBy('position');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function isRead()
    {
        return !is_null($this->read_at);
    }
}
