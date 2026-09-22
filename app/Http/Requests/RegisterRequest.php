<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Closure;

class RegisterRequest extends FormRequest
{
    /**
     * Tentukan apakah user bisa membuat request ini
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules yang apply ke request ini
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:100',
            'phone' => ['required', 'unique:users,phone', 'max:20', $this->validatePhoneNumber()],
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ];
    }

    /**
     * Validate phone number format
     */
    private function validatePhoneNumber(): Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) {
            // Match: +6212345678901 or 08123456789
            if (!preg_match('/^(\+62|0)[0-9]{9,12}$/', $value)) {
                $fail('The ' . $attribute . ' field must be a valid phone number.');
            }
        };
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama harus diisi',
            'name.max' => 'Nama maksimal 100 karakter',
            
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'email.max' => 'Email maksimal 100 karakter',
            
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.unique' => 'Nomor telepon sudah terdaftar',
            'phone.max' => 'Nomor telepon maksimal 20 karakter',
            'phone.regex' => 'Nomor telepon harus format yang valid (dimulai dengan +62 atau 0)',
            
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            
            'password_confirmation.required' => 'Konfirmasi password harus diisi',
        ];
    }
}