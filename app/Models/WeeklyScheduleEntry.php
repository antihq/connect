<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyScheduleEntry extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available' => 'boolean',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
