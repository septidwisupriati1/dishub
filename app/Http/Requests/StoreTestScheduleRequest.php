<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestScheduleRequest extends FormRequest
{
    /**
     * Tentukan apakah user bisa membuat request ini
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules yang apply ke request ini
     */
    public function rules(): array
    {
        return [
            'test_date' => 'required|date|after_or_equal:today|unique:test_schedules,test_date',
            'max_queue' => 'required|integer|min:1|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'test_date.required' => 'Tanggal ujian harus diisi',
            'test_date.date' => 'Tanggal ujian harus format tanggal yang valid',
            'test_date.after_or_equal' => 'Tanggal ujian harus hari ini atau lebih',
            'test_date.unique' => 'Jadwal ujian untuk tanggal ini sudah ada',
            
            'max_queue.required' => 'Jumlah antrian maksimal harus diisi',
            'max_queue.integer' => 'Jumlah antrian maksimal harus berupa angka',
            'max_queue.min' => 'Jumlah antrian maksimal minimal 1',
            'max_queue.max' => 'Jumlah antrian maksimal maksimal 100',
            
            'start_time.required' => 'Jam mulai harus diisi',
            'start_time.date_format' => 'Jam mulai harus format HH:mm',
            
            'end_time.required' => 'Jam selesai harus diisi',
            'end_time.date_format' => 'Jam selesai harus format HH:mm',
            'end_time.after' => 'Jam selesai harus lebih besar dari jam mulai',
            
            'notes.max' => 'Catatan maksimal 500 karakter',
        ];
    }

    /**
     * Custom validation untuk logic tambahan
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Cek tidak boleh jadwal di weekend
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $this->test_date);
            if ($date->isWeekend()) {
                $validator->errors()->add('test_date', 'Jadwal ujian tidak boleh pada hari weekend');
            }
        });
    }
}