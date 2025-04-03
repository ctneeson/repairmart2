<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'iso_code',
        'name'
    ];

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'currency_id', 'id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'currency_id', 'id');
    }
}
