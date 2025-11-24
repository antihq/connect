<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration' => 'integer',
            'price_per_unit' => 'integer',
            'total' => 'integer',
        ];
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Get the price per unit in dollars.
     */
    public function getPricePerUnitDollarsAttribute(): float
    {
        return $this->price_per_unit / 100;
    }

    /**
     * Get the total in dollars.
     */
    public function getTotalDollarsAttribute(): float
    {
        return $this->total / 100;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->hasMany(TransactionActivity::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}
