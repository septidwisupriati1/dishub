<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TestResult;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_number',
        'owner_name',
        'address',
        'vehicle_type',
        'brand',
        'model',
        'usage_type',
        'fuel_type',
        'year',
        'color',
        'engine_number',
        'chassis_number',
        'test_count',
        'last_test_date',
        'status',
    ];

    protected $casts = [
        'last_test_date' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'under_maintenance');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('vehicle_type', $type);
    }

    // Methods
    public function getLastTestResult()
    {
        return $this->testResults()->latest()->first();
    }

    public function getLastTestStatus()
    {
        $lastResult = $this->getLastTestResult();
        return $lastResult ? $lastResult->overall_status : null;
    }

    public function isEligibleForTest(): bool
    {
        return $this->status === 'active' && $this->is_active !== false;
    }
}