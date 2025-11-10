<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionActivity extends Model
{
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    /** @use HasFactory<\Database\Factories\TransactionActivityFactory> */
    use HasFactory;
}
