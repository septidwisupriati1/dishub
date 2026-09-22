<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQueueStatusRequest extends FormRequest
{
    /**
     * Tentukan apakah user bisa membuat request ini
     */
    public function authorize(): bool
    {
        // PERBAIKAN: gunakan pengecekan role langsung
        // agar tidak error jika method isPenguji() tidak ada di model User
        return auth()->check() && auth()->user()->role === 'penguji';
    }

    /**
     * Get the validation rules yang apply ke request ini
     */
    public function rules(): array
    {
        return [
            'status'         => 'required|in:waiting,in_progress,completed,cancelled',
            'estimated_time' => 'nullable|integer|min:0|max:120',
            'notes'          => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'status.required'        => 'Status antrian harus diisi',
            'status.in'              => 'Status antrian tidak valid',
            'estimated_time.integer' => 'Estimasi waktu harus berupa angka',
            'estimated_time.min'     => 'Estimasi waktu minimal 0 menit',
            'estimated_time.max'     => 'Estimasi waktu maksimal 120 menit',
            'notes.max'              => 'Catatan maksimal 500 karakter',
        ];
    }
}