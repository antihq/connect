<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
            'timezone' => 'string',
            'price' => 'integer',
            'photos' => 'array', // Store photo paths as array
        ];
    }

    public function weeklyScheduleEntries()
    {
        return $this->hasMany(WeeklyScheduleEntry::class);
    }

    public function availabilityExceptions()
    {
        return $this->hasMany(AvailabilityException::class);
    }

    public function isPublishable(): bool
    {
        return filled($this->title)
            && filled($this->description)
            && filled($this->address)
            && is_numeric($this->price) && $this->price > 0
            && $this->weeklyScheduleEntries()->count() > 0
            && is_array($this->photos) && count($this->photos) > 0;
    }

    /**
     * Get and set the price in dollars.
     */
    protected function priceDollars(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['price'] !== null ? round($attributes['price'] / 100, 2) : 0.0,
            set: fn ($value) => ['price' => (int) round($value * 100)],
        );
    }

    public function syncWeeklySchedule(array $days): void
    {
        $this->weeklyScheduleEntries()->delete();
        collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->each(function ($day) use ($days) {
            $this->weeklyScheduleEntries()->create([
                'day' => $day,
                'available' => in_array($day, $days),
            ]);
        });
    }

    public function syncAvailabilityExceptions(array $exceptions): void
    {
        $this->availabilityExceptions()->delete();
        collect($exceptions)->each(function ($exception) {
            $this->availabilityExceptions()->create($exception);
        });
    }
}
