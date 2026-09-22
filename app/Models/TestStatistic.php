<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'penguji_id',
        'test_date',
        'total_tested',
        'total_passed',
        'total_failed',
        'pass_percentage',
        'avg_duration_minutes',
    ];

    protected $casts = [
        'test_date' => 'date',
    ];

    // Relationships
    public function penguji(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }

    // Scopes
    public function scopeByPenguji($query, $pengujiId)
    {
        return $query->where('penguji_id', $pengujiId);
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
    public function getFailureCount(): int
    {
        return $this->total_tested - $this->total_passed;
    }

    public function getAvgDurationInHours(): float
    {
        return round($this->avg_duration_minutes / 60, 2);
    }

    public function isGoodPerformance(): bool
    {
        return $this->pass_percentage >= 80;
    }

    public function isBelowTarget(): bool
    {
        return $this->pass_percentage < 60;
    }
}