<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'listing_id',
        'order_id',
        'email_id',
        'position',
        'path',
        'mime_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function listings(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function emails(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }

    public function getUrl()
    {
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }
        return Storage::url($this->path);
    }
}
