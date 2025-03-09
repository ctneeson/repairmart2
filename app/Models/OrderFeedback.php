<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrderFeedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'user_id',
        'feedback_type_id',
        'comments',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function feedbackType(): BelongsToMany
    {
        return $this->belongsToMany(
            FeedbackType::class, // The related model
            'orders_feedback', // The pivot table
            'order_id', // Foreign key on the pivot table for the current model
            'feedback_type_id' // Foreign key on the pivot table for the related model
        );
    }
}
