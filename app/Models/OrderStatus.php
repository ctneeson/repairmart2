<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'amount_editable',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
