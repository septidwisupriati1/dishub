<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQueueRequest extends FormRequest
{
    /**
     * Tentukan apakah user bisa membuat request ini
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isPeserta();
    }

    /**
     * Get the validation rules yang apply ke request ini
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                'integer',
                Rule::exists('vehicles', 'id')->where('user_id', auth()->id()),
            ],
            'queue_date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addDays(30)->format('Y-m-d'),
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'ID kendaraan harus diisi',
            'vehicle_id.integer' => 'ID kendaraan harus berupa angka',
            'vehicle_id.exists' => 'Kendaraan tidak ditemukan',
            
            'queue_date.required' => 'Tanggal ujian harus diisi',
            'queue_date.date' => 'Tanggal ujian harus format tanggal yang valid',
            'queue_date.after_or_equal' => 'Tanggal ujian harus hari ini atau lebih',
            'queue_date.before_or_equal' => 'Tanggal ujian maksimal 30 hari ke depan',
        ];
    }

    /**
     * Custom validation untuk logic tambahan
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Cek apakah kendaraan aktif
            $vehicle = \App\Models\Vehicle::find($this->vehicle_id);
            if ($vehicle && $vehicle->status !== 'active') {
                $validator->errors()->add('vehicle_id', 'Kendaraan tidak aktif untuk pengujian');
            }

            // Cek jadwal tersedia
            $schedule = \App\Models\TestSchedule::where('test_date', $this->queue_date)->first();
            if (!$schedule) {
                $validator->errors()->add('queue_date', 'Jadwal ujian tidak tersedia untuk tanggal ini');
            } elseif ($schedule->status === 'full') {
                $validator->errors()->add('queue_date', 'Slot antrian sudah penuh untuk tanggal ini');
            }

            // Cek sudah ada antrian di hari yang sama
            $existingQueue = \App\Models\Queue::where('vehicle_id', $this->vehicle_id)
                ->whereDate('queue_date', $this->queue_date)
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingQueue) {
                $validator->errors()->add('vehicle_id', 'Kendaraan sudah memiliki antrian pada tanggal ini');
            }
        });
    }
}