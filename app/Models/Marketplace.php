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
            'require_user_approval_action' => 'string',
            'require_user_approval_internal_link' => 'string',
            'require_user_approval_internal_text' => 'string',
            'require_user_approval_external_link' => 'string',
            'require_user_approval_external_text' => 'string',

            'restrict_view_listings_action' => 'string',
            'restrict_view_listings_internal_link' => 'string',
            'restrict_view_listings_internal_text' => 'string',
            'restrict_view_listings_external_link' => 'string',
            'restrict_view_listings_external_text' => 'string',

            'restrict_posting_action' => 'string',
            'restrict_posting_internal_link' => 'string',
            'restrict_posting_internal_text' => 'string',
            'restrict_posting_external_link' => 'string',
            'restrict_posting_external_text' => 'string',

            'restrict_transactions_action' => 'string',
            'restrict_transactions_internal_link' => 'string',
            'restrict_transactions_internal_text' => 'string',
            'restrict_transactions_external_link' => 'string',
            'restrict_transactions_external_text' => 'string',

            'require_listing_approval_action' => 'string',
            'require_listing_approval_internal_link' => 'string',
            'require_listing_approval_internal_text' => 'string',
            'require_listing_approval_external_link' => 'string',
            'require_listing_approval_external_text' => 'string',
        ];
    }

    // Add casts() if you have attributes to cast in the future
}
