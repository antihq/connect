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

    // Add casts() if you have attributes to cast in the future
}
