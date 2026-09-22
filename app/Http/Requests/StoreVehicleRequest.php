<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
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
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number|max:20',
            'vehicle_type' => 'required|in:motorcycle,car,truck,bus',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'engine_number' => 'required|string|unique:vehicles,engine_number|max:100',
            'chassis_number' => 'required|string|unique:vehicles,chassis_number|max:100',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'vehicle_number.required' => 'Nomor polisi harus diisi',
            'vehicle_number.unique' => 'Nomor polisi sudah terdaftar di sistem',
            'vehicle_number.max' => 'Nomor polisi maksimal 20 karakter',
            
            'vehicle_type.required' => 'Jenis kendaraan harus dipilih',
            'vehicle_type.in' => 'Jenis kendaraan tidak valid',
            
            'brand.required' => 'Merek kendaraan harus diisi',
            'brand.max' => 'Merek kendaraan maksimal 100 karakter',
            
            'model.required' => 'Model kendaraan harus diisi',
            'model.max' => 'Model kendaraan maksimal 100 karakter',
            
            'year.required' => 'Tahun kendaraan harus diisi',
            'year.integer' => 'Tahun harus berupa angka',
            'year.min' => 'Tahun kendaraan minimal 1900',
            'year.max' => 'Tahun kendaraan tidak boleh melebihi tahun depan',
            
            'color.required' => 'Warna kendaraan harus diisi',
            'color.max' => 'Warna kendaraan maksimal 50 karakter',
            
            'engine_number.required' => 'Nomor mesin harus diisi',
            'engine_number.unique' => 'Nomor mesin sudah terdaftar di sistem',
            'engine_number.max' => 'Nomor mesin maksimal 100 karakter',
            
            'chassis_number.required' => 'Nomor rangka harus diisi',
            'chassis_number.unique' => 'Nomor rangka sudah terdaftar di sistem',
            'chassis_number.max' => 'Nomor rangka maksimal 100 karakter',
        ];
    }

    /**
     * Prepare data untuk disimpan ke database
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        
        if (is_null($key)) {
            $data['user_id'] = auth()->id();
        }
        
        return $data;
    }
}