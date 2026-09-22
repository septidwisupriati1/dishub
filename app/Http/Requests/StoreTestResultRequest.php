<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'queue_id' => 'required|exists:queues,id',
            'vehicle_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            'emission_status' => 'nullable|in:pass,fail',
            'emission_notes' => 'nullable|string',
            'brake_status' => 'nullable|in:pass,fail',
            'brake_notes' => 'nullable|string',
            'light_status' => 'nullable|in:pass,fail',
            'light_notes' => 'nullable|string',
            'horn_status' => 'nullable|in:pass,fail',
            'horn_notes' => 'nullable|string',
            'suspension_status' => 'nullable|in:pass,fail',
            'suspension_notes' => 'nullable|string',
            'tire_status' => 'nullable|in:pass,fail',
            'tire_notes' => 'nullable|string',

            'overall_notes' => 'nullable|string',
        ];
    }
}