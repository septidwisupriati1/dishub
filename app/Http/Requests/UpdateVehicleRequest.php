<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    /**
     * Tentukan apakah user bisa membuat request ini
     */
    public function authorize(): bool
    {
        $vehicle = $this->route('vehicle');

        // PERBAIKAN: gunakan == (loose) bukan === (strict)
        // agar tidak gagal karena perbedaan tipe int vs string
        return auth()->check() && auth()->id() == $vehicle->user_id;
    }

    /**
     * Get the validation rules yang apply ke request ini
     */
    public function rules(): array
    {
        return [
            'vehicle_type' => 'sometimes|in:motorcycle,car,truck,bus',
            'brand'        => 'sometimes|string|max:100',
            'model'        => 'sometimes|string|max:100',
            'year'         => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'color'        => 'sometimes|string|max:50',
            'status'       => 'sometimes|in:active,inactive,under_maintenance',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'vehicle_type.in' => 'Jenis kendaraan tidak valid',
            'brand.max'       => 'Merek kendaraan maksimal 100 karakter',
            'model.max'       => 'Model kendaraan maksimal 100 karakter',
            'year.integer'    => 'Tahun harus berupa angka',
            'year.min'        => 'Tahun kendaraan minimal 1900',
            'year.max'        => 'Tahun kendaraan tidak boleh melebihi tahun depan',
            'color.max'       => 'Warna kendaraan maksimal 50 karakter',
            'status.in'       => 'Status kendaraan tidak valid',
        ];
    }
}