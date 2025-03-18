<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // protected $primaryKey = 'userId';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'google_id',
        'facebook_id',
        'address_line1',
        'address_line2',
        'city',
        'country_id',
        'postcode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function emailsSent(): HasMany
    {
        return $this->hasMany(Email::class, 'from_id');
    }

    public function emailsReceived(): BelongsToMany
    {
        return $this->belongsToMany(
            Email::class, // The related model
            'emails_recipients', // The pivot table
            'recipient_id', // Foreign key on the pivot table for the current model
            'email_id' // Foreign key on the pivot table for the related model
        );
    }

    public function listingsCreated(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function listingsQuoted(): HasManyThrough
    {
        return $this->hasManyThrough(
            Listing::class, // The final model we want to access
            Quote::class, // The intermediate model
            'user_id', // Foreign key on the quotes table
            'listing_id', // Foreign key on the listings table
            'id', // Local key on the users table
            'id' // Local key on the quotes table
        );
    }

    public function quotesCreated(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function quotesReceived(): HasManyThrough
    {
        return $this->hasManyThrough(
            Quote::class, // The final model we want to access
            Listing::class, // The intermediate model
            'user_id', // Foreign key on the listings table
            'listing_id', // Foreign key on the quotes table
            'id', // Local key on the users table
            'id' // Local key on the listings table
        );
    }

    public function customerOrders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Order::class, // The final model we want to access
            Quote::class, // The intermediate model
            'listing_id', // Foreign key on the quotes table
            'quote_id', // Foreign key on the orders table
            'id', // Local key on the users table
            'id' // Local key on the listings table
        );
    }

    public function repairSpecialistOrders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Order::class, // The final model we want to access
            Quote::class, // The intermediate model
            'user_id', // Foreign key on the quotes table
            'quote_id', // Foreign key on the orders table
            'id', // Local key on the users table
            'id' // Local key on the quotes table
        );
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
