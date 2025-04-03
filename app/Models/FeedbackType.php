<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function orderFeedback(): HasMany
    {
        return $this->hasMany(OrderFeedback::class);
    }
}
