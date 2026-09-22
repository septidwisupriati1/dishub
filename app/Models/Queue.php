<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\TestResult;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'test_schedule_id',
        'queue_date',
        'queue_number',
        'status',
        'notes',
        'called_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'queue_date' => 'date',
        'called_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected $appends = ['formatted_queue_number'];

    public function getFormattedQueueNumberAttribute()
    {
        $prefix = 'U';
        if ($this->vehicle && $this->vehicle->vehicle_type) {
            $type = strtolower($this->vehicle->vehicle_type);
            if ($type === 'car' || $type === 'mobil') $prefix = 'C';
            elseif ($type === 'motorcycle' || $type === 'motor') $prefix = 'M';
            elseif ($type === 'truck' || $type === 'truk') $prefix = 'T';
            elseif ($type === 'bus') $prefix = 'B';
            else $prefix = strtoupper(substr($type, 0, 1));
        }
        return $prefix . '-' . str_pad($this->queue_number, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function testSchedule(): BelongsTo
    {
        return $this->belongsTo(TestSchedule::class);
    }

    public function testResult(): HasOne
    {
        return $this->hasOne(TestResult::class);
    }

    // Scopes
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('queue_date', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Methods
    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->completed_at->diffInMinutes($this->started_at);
        }
        return null;
    }

    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCalled(): bool
    {
        return $this->status === 'waiting';
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'waiting';
    }
}