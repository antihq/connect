<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    /** @use HasFactory<\Database\Factories\MarketplaceFactory> */
    use HasFactory;

    protected $guarded = [];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'require_user_approval' => 'boolean',
            'restrict_view_listings' => 'boolean',
            'restrict_posting' => 'boolean',
            'restrict_transactions' => 'boolean',
            'require_listing_approval' => 'boolean',
            'sender_email_name' => 'string',
        ];
    }

    // Add casts() if you have attributes to cast in the future
}
