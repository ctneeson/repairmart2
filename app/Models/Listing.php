<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'status_id',
        'manufacturer_id',
        'title',
        'description',
        'currency_id',
        'budget',
        'use_default_location',
        'address_line1',
        'address_line2',
        'city',
        'postcode',
        'country_id',
        'phone',
        'expiry_days',
        'published_at',
        'expired_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'published_at',
        'expired_at',
    ];

    protected $casts = [
        'use_default_location' => 'boolean',
        'budget' => 'decimal:2',
        'expiry_days' => 'integer',
        'published_at' => 'datetime',
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // public $timestamps = false;

    /*
     * Get the user who created the listing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
     * Get the status of the listing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ListingStatus::class, 'status_id');
    }

    /*
     * Get the manufacturer for the item being listed.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    /*
     * Get the currency associated with the listing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /*
     * Get the country associated with the listing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /*
     * Get the primary attachment associated with the listing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryAttachment(): HasOne
    {
        return $this->hasOne(Attachment::class, 'listing_id')
            ->oldestOfMany('position');
    }

    /*
     * Get the attachments associated with the listing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments(): HasMany
    {
        return $this->HasMany(Attachment::class, 'listing_id')->orderBy('position');
    }

    /*
     * Get the quotes associated with the listing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quotes(): HasMany
    {
        return $this->HasMany(Quote::class, 'listing_id');
    }

    /*
     * Get the users who have submitted quotes for the listing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function quotesReceivedFrom(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class, // The final model we want to access
            Quote::class, // The intermediate model
            'listing_id', // Foreign key on the quotes table
            'user_id', // Foreign key on the users table
            'id', // Local key on the listings table
            'id' // Local key on the quotes table
        );
    }

    /*
     * Get the order associated with the listing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function order(): HasOneThrough
    {
        return $this->hasOneThrough(
            Order::class, // The final model we want to access
            Quote::class, // The intermediate model
            'listing_id', // Foreign key on the quotes table
            'quote_id', // Foreign key on the orders table
            'id', // Local key on the listings table
            'id' // Local key on the quotes table
        );
    }

    /*
     * Get the user who is assigned to the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function orderAssignedTo(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class, // The final model we want to access
            Quote::class, // The intermediate model
            'listing_id', // Foreign key on the quotes table
            'user_id', // Foreign key on the users table
            'id', // Local key on the listings table
            'id' // Local key on the quotes table
        );
    }

    /*
     * Get the product categories assigned to the item being listed.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class, // The related model
            'listings_products', // The pivot table
            'listing_id', // Foreign key on the pivot table for the current model
            'product_id' // Foreign key on the pivot table for the related model
        );
    }

    /*
     * Get the emails associated with the listing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails(): HasMany
    {
        return $this->HasMany(Email::class, 'listing_id');
    }

    /**
     * Get the created date in 'Y-m-d' format.
     *
     * @return string
     */
    public function getCreatedDate(): string
    {
        return (new Carbon($this->created_at))->format('Y-m-d');
    }

    /**
     * Get the published date in 'Y-m-d' format.
     *
     * @return string
     */
    public function getPublishedDate(): string
    {
        return (new Carbon($this->published_at))->format('Y-m-d');
    }

    /**
     * Get the users who have added the listing to their watchlist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function watchlistUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favourite_listings');
    }

    /**
     * Check if the listing is in the user's watchlist.
     *
     * @return bool
     */
    public function isInWatchlist(): bool
    {
        return $this->watchlistUsers->contains(Auth::user());
    }

    /**
     * Scope a query to only include open listings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen(Builder $query): Builder
    {
        $openStatusId = cache()->remember('listing_status_open', 60 * 24, function () {
            return ListingStatus::where('name', 'Open')->first()->id ?? null;
        });

        return $query->where('status_id', $openStatusId);
    }

    /**
     * Scope a query to only include published listings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished(Builder $query): Builder
    {
        $now = now()->setTimezone('UTC');
        return $query->where('published_at', '<=', $now);
    }

    /**
     * Scope a query to only include listings that haven't expired.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        // Use UTC consistently with published_at
        $today = Carbon::now()->setTimezone('UTC')->startOfDay();
        $earliestValidPublishDate = $today->copy()->subDays(config('app.max_listing_expiry_days', 90));

        return $query->where(function ($query) use ($today, $earliestValidPublishDate) {
            // For SQLite (which you seem to be using)
            $driver = \DB::connection()->getDriverName();

            if ($driver === 'sqlite') {
                // SQLite date calculation
                $query->where('published_at', '>=', $earliestValidPublishDate)
                    ->whereRaw("date(published_at, '+' || expiry_days || ' days') >= ?", [$today->format('Y-m-d')]);
            } elseif ($driver === 'mysql') {
                // MySQL date calculation
                $query->where('published_at', '>=', $earliestValidPublishDate)
                    ->whereRaw("DATE_ADD(published_at, INTERVAL expiry_days DAY) >= ?", [$today->format('Y-m-d')]);
            } elseif ($driver === 'pgsql') {
                // PostgreSQL date calculation
                $query->where('published_at', '>=', $earliestValidPublishDate)
                    ->whereRaw("(published_at + (expiry_days || ' days')::interval) >= ?", [$today->format('Y-m-d')]);
            } elseif ($driver === 'sqlsrv') {
                // SQL Server date calculation
                $query->where('published_at', '>=', $earliestValidPublishDate)
                    ->whereRaw("DATEADD(day, expiry_days, published_at) >= ?", [$today->format('Y-m-d')]);
            } else {
                // Generic fallback using julianday (though this may not work on all databases)
                $query->where('published_at', '>=', $earliestValidPublishDate)
                    ->whereRaw('(julianday(?) - julianday(published_at)) <= expiry_days', [$today->format('Y-m-d')]);
            }
        });
    }

    /**
     * Scope a query to only include active listings (published, open, not expired).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->published()->open()->notExpired();
    }

    /**
     * Get the expiry date attribute.
     *
     * @return \Carbon\Carbon|null
     */
    public function getExpiryDateAttribute()
    {
        if (!$this->published_at) {
            return null;
        }

        $publishedAt = $this->published_at instanceof Carbon
            ? $this->published_at
            : Carbon::parse($this->published_at);

        return $publishedAt->addDays($this->expiry_days);
    }

    /**
     * Determine if the listing is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->published_at) {
            return false;
        }

        return $this->expiry_date->lt(Carbon::now()->startOfDay());
    }

    /**
     * Scope a query to order by expiry date.
     */
    public function scopeOrderByExpiryDate($query, $direction = 'asc')
    {
        $driver = \DB::connection()->getDriverName();

        switch ($driver) {
            case 'mysql':
                return $query->orderByRaw("DATE_ADD(published_at, INTERVAL expiry_days DAY) {$direction}");

            case 'sqlite':
                return $query->orderByRaw("date(published_at, '+' || expiry_days || ' days') {$direction}");

            case 'pgsql':
                return $query->orderByRaw("(published_at + (expiry_days || ' days')::interval) {$direction}");

            case 'sqlsrv':
                return $query->orderByRaw("DATEADD(day, expiry_days, published_at) {$direction}");

            default:
                // Fallback for other database systems
                return $query->orderBy('published_at', $direction)
                    ->orderBy('expiry_days', $direction);
        }
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();

        // Update FTS when a listing is saved
        static::saved(function (Listing $listing) {
            if (\DB::connection()->getDriverName() === 'sqlite') {
                $title = str_replace("'", "''", $listing->title);
                $description = str_replace("'", "''", $listing->description);

                // Delete any existing entry
                \DB::statement("DELETE FROM listings_fts WHERE rowid = {$listing->id}");

                // Insert the new entry
                \DB::statement("INSERT INTO listings_fts(rowid, title, description) 
                            VALUES ({$listing->id}, '{$title}', '{$description}')");
            }
        });

        // Remove from FTS when a listing is deleted
        static::deleted(function (Listing $listing) {
            if (\DB::connection()->getDriverName() === 'sqlite') {
                \DB::statement("DELETE FROM listings_fts WHERE rowid = {$listing->id}");
            }
        });
    }
}
