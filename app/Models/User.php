<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
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
        'postcode',
        'country_id',
        'email_verified_at',
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
        return $this->hasMany(Email::class, 'sender_id');
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
            'id', // Foreign key on the listings table
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

    /**
     * Get orders where the user is the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get orders where the user is the specialist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repairSpecialistOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'specialist_id');
    }

    /**
     * Get the user's roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    /**
     * Check if the user has any of the specified roles.
     * 
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        // Debug log
        \Log::debug('Checking roles: ' . json_encode($roles));
        \Log::debug('User roles: ' . json_encode($this->roles->pluck('name')));

        // Convert to array if it's a single role
        $roles = is_array($roles) ? $roles : [$roles];

        // Get all the user's role names
        $userRoleNames = $this->roles->pluck('name')->toArray();

        // Check for intersection
        foreach ($roles as $role) {
            if (in_array($role, $userRoleNames)) {
                \Log::debug('Role match found: ' . $role);
                return true;
            }
        }

        \Log::debug('No role match found');
        return false;
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function watchlistListings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'favourite_listings')
            ->withPivot('id')
            ->orderBy('favourite_listings.id', 'desc');
    }

    public function isOauthUser(): bool
    {
        return $this->google_id !== null || $this->facebook_id !== null;
    }

    public function hasAddress(): bool
    {
        return !empty($this->address_line1) &&
            !empty($this->city) &&
            !empty($this->postcode) &&
            !empty($this->country_id);
    }

    public function getCreatedDate(): string
    {
        return (new Carbon($this->created_at))->format('Y-m-d');
    }
}
