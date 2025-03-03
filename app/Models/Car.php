<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'maker_id',
        'model_id',
        'year',
        'price',
        'vin',
        'mileage',
        'car_type_id',
        'fuel_type_id',
        'user_id',
        'city_id',
        'address',
        'phone',
        'description',
        'published_at',
    ];

    // protected $guarded = ['user_id'];
    public function carType(): BelongsTo
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_id');
    }

    public function maker(): BelongsTo
    {
        return $this->belongsTo(Maker::class, 'maker_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(Model::class, 'model_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function features(): HasOne
    {
        return $this->hasOne(CarFeatures::class, 'car_id');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(CarImage::class, 'car_id')
            ->oldestOfMany('position');
    }

    public function images(): HasMany
    {
        return $this->HasMany(CarImage::class, 'car_id');
    }

    public function favouredUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favourite_cars', 'car_id', 'user_id');
        // ->withTimestamps();
    }
}
