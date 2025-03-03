<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuelType extends Model
{
    use HasFactory;
    // protected $table = 'car_fuel_types';
    // protected $primaryKey = 'fuelTypeId';
    // const CREATED_AT = 'DATE_INSERTED';
    // const UPDATED_AT = 'DATE_UPDATED';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
