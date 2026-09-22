<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestScheduleRequest extends FormRequest
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
            'max_queue' => 'sometimes|integer|min:1|max:100',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'status' => 'sometimes|in:open,closed,full',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'max_queue.integer' => 'Jumlah antrian maksimal harus berupa angka',
            'max_queue.min' => 'Jumlah antrian maksimal minimal 1',
            'max_queue.max' => 'Jumlah antrian maksimal maksimal 100',
            
            'start_time.date_format' => 'Jam mulai harus format HH:mm',
            'end_time.date_format' => 'Jam selesai harus format HH:mm',
            
            'status.in' => 'Status jadwal tidak valid',
            'notes.max' => 'Catatan maksimal 500 karakter',
        ];
    }

    /**
     * Custom validation untuk logic tambahan
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Jika ada start_time dan end_time, cek end_time > start_time
            if ($this->start_time && $this->end_time) {
                $start = \Carbon\Carbon::createFromFormat('H:i', $this->start_time);
                $end = \Carbon\Carbon::createFromFormat('H:i', $this->end_time);
                
                if ($end->lte($start)) {
                    $validator->errors()->add('end_time', 'Jam selesai harus lebih besar dari jam mulai');
                }
            }
        });
    }
}