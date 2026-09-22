<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_number',
        'queue_id',
        'vehicle_id',
        'vehicle_photo',
        'penguji_id',
        'emission_status',
        'emission_notes',
        'brake_status',
        'brake_notes',
        'light_status',
        'light_notes',
        'horn_status',
        'horn_notes',
        'suspension_status',
        'suspension_notes',
        'tire_status',
        'tire_notes',
        'overall_status',
        'overall_notes',
        'tested_at',
        'valid_until',
    ];

    protected $casts = [
         'tested_at' => 'datetime',
         'valid_until' => 'date',
    ];

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function penguji(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }

    /**
     * Dapatkan URL foto kendaraan yang valid di semua device
     * Menggunakan Storage::url() untuk path yang relatif
     */
    public function getPhotoUrl()
    {
        if (!$this->vehicle_photo) {
            return null;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->vehicle_photo);
    }
}