<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    /** @use HasFactory<\Database\Factories\ListingFactory> */
    use HasFactory;

    protected $guarded = [];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'weekly_schedule' => 'array',
            'availability_exceptions' => 'array',
            'timezone' => 'string',
            'price' => 'integer',
            'photos' => 'array', // Store photo paths as array
        ];
    }

    public function isPublishable(): bool
    {
        return filled($this->title)
            && filled($this->description)
            && filled($this->address)
            && is_numeric($this->price) && $this->price > 0
            && is_array($this->weekly_schedule) && count($this->weekly_schedule) > 0
            && is_array($this->photos) && count($this->photos) > 0;
    }

    /**
     * Get the price in dollars.
     */
    public function getPriceDollarsAttribute(): float
    {
        return $this->price !== null ? $this->price / 100 : 0.0;
    }

    /**
     * Set the price in dollars.
     */
    public function setPriceDollarsAttribute($value): void
    {
        $this->attributes['price'] = (int) round($value * 100);
    }
}
