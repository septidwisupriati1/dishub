<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * TestSchedule Model
 * 
 * @property int $id
 * @property Carbon $test_date
 * @property string $day_of_week
 * @property int $max_queue
 * @property int $current_queue
 * @property string $status
 * @property string $start_time
 * @property string $end_time
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TestSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_date',
        'day_of_week',
        'max_queue',
        'current_queue',
        'status',
        'start_time',
        'end_time',
        'notes',
    ];

    protected $casts = [
        'test_date' => 'datetime',
    ];

    // Relationships
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeFull($query)
    {
        return $query->where('status', 'full');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('test_date', $date);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereDate('test_date', '>=', $startDate)
                     ->whereDate('test_date', '<=', $endDate);
    }

    // Methods
    public function getAvailableSlots(): int
    {
        return max(0, $this->max_queue - $this->current_queue);
    }

    public function isFull(): bool
    {
        return $this->current_queue >= $this->max_queue;
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function getOccupancyPercentage(): float
    {
        if ($this->max_queue === 0) {
            return 0;
        }
        return ($this->current_queue / $this->max_queue) * 100;
    }
}