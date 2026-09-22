<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Closure;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        try {
            // Validate input
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ], [
                'email.required' => 'Email harus diisi',
                'email.email' => 'Format email tidak valid',
                'password.required' => 'Password harus diisi',
            ]);

            // Cek user exists
            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Email atau password salah'], 401);
                }
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Email tidak terdaftar']);
            }

            // Cek password
            if (!Hash::check($credentials['password'], $user->password)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Email atau password salah'], 401);
                }
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['password' => 'Password tidak sesuai']);
            }

            // Cek user aktif
            if (!$user->is_active) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.'], 403);
                }
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
            }

            // Generate token for API
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return API response or web response
            if ($request->wantsJson()) {
                // Update last login
                $user->update(['last_login_at' => now()]);

                return response()->json([
                    'message' => 'Login berhasil!',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                    'token' => $token,
                ], 200);
            }

            // Login berhasil (web)
            auth()->login($user, $request->boolean('remember'));

            // Update last login
            $user->update(['last_login_at' => now()]);

            // Regenerate session
            $request->session()->regenerate();

            return redirect()
                ->intended(route('dashboard'))
                ->with('success', 'Login berhasil! Selamat datang ' . $user->name);

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            return back()
                ->withInput($request->only('email'))
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat login.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Terjadi kesalahan saat login. Silakan coba lagi.']);
        }
    }

    /**
     * Show register form
     */
    public function showRegister()
    {
        // Jika sudah login, redirect ke dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.register');
    }

    /**
     * Handle register
     */
    public function register(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email|max:100',
                'phone' => ['required', 'unique:users,phone', 'max:20', $this->validatePhoneNumber()],
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required',
            ], [
                'name.required' => 'Nama harus diisi',
                'name.max' => 'Nama maksimal 100 karakter',
                
                'email.required' => 'Email harus diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah terdaftar',
                'email.max' => 'Email maksimal 100 karakter',
                
                'phone.required' => 'Nomor telepon harus diisi',
                'phone.unique' => 'Nomor telepon sudah terdaftar',
                'phone.max' => 'Nomor telepon maksimal 20 karakter',
                'phone.regex' => 'Format nomor telepon tidak valid (dimulai dengan +62 atau 0)',
                
                'password.required' => 'Password harus diisi',
                'password.min' => 'Password minimal 8 karakter',
                'password.confirmed' => 'Konfirmasi password tidak sesuai',
                
                'password_confirmation.required' => 'Konfirmasi password harus diisi',
            ]);

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'peserta', // Default role
                'is_active' => true,
            ]);

            // Trigger registered event (untuk email verification jika ada)
            event(new Registered($user));

            // Generate token for API
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return API response or web response
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Registrasi berhasil!',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                    'token' => $token,
                ], 201);
            }

            return redirect()
                ->route('login')
                ->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()
                ->withInput($request->only('name', 'email', 'phone'))
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Register error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat registrasi.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput($request->only('name', 'email', 'phone'))
                ->withErrors(['email' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.']);
        }
    }

    /**
     * Show profile edit form
     */
    public function showProfile()
    {
        $user = auth()->user();
        return view('auth.profile', compact('user'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();

            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'phone' => ['required', 'unique:users,phone,' . $user->id, 'max:20', $this->validatePhoneNumber()],
            ], [
                'name.required' => 'Nama harus diisi',
                'name.max' => 'Nama maksimal 100 karakter',
                
                'phone.required' => 'Nomor telepon harus diisi',
                'phone.unique' => 'Nomor telepon sudah digunakan',
                'phone.max' => 'Nomor telepon maksimal 20 karakter',
            ]);

            // Update user
            $user->update($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Profile berhasil diperbarui!',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                    ],
                ], 200);
            }

            return redirect()
                ->route('profile.edit')
                ->with('success', 'Profile berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            return back()
                ->withInput($request->only('name', 'phone'))
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Update profile error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat update profile.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput($request->only('name', 'phone'))
                ->withErrors(['name' => 'Terjadi kesalahan saat update profile.']);
        }
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    /**
     * Handle change password
     */
    public function changePassword(Request $request)
    {
        try {
            $user = auth()->user();

            // Validate input
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
                'new_password_confirmation' => 'required',
            ], [
                'current_password.required' => 'Password lama harus diisi',
                
                'new_password.required' => 'Password baru harus diisi',
                'new_password.min' => 'Password baru minimal 8 karakter',
                'new_password.confirmed' => 'Konfirmasi password baru tidak sesuai',
                
                'new_password_confirmation.required' => 'Konfirmasi password baru harus diisi',
            ]);

            // Cek password lama
            if (!Hash::check($validated['current_password'], $user->password)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Password lama tidak sesuai'], 422);
                }
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
            }

            // Cek password baru berbeda dengan password lama
            if (Hash::check($validated['new_password'], $user->password)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Password baru harus berbeda dengan password lama'], 422);
                }
                return back()->withErrors(['new_password' => 'Password baru harus berbeda dengan password lama']);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['new_password']),
            ]);

            // Logout semua device lain (opsional)
            // $user->tokens()->delete();

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Password berhasil diubah',
                ], 200);
            }

            return redirect()
                ->route('profile.change-password')
                ->with('success', 'Password berhasil diubah!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Change password error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat mengubah password.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withErrors(['current_password' => 'Terjadi kesalahan saat mengubah password.']);
        }
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
     * Handle logout
     */
    public function logout(Request $request)
    {
        try {
            if ($request->wantsJson()) {
                // API logout - revoke token
                $request->user()->currentAccessToken()->delete();
                
                return response()->json([
                    'message' => 'Logout berhasil',
                ], 200);
            }

            // Web logout
            auth()->logout();

            // Invalidate session
            $request->session()->invalidate();

            // Regenerate token
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('success', 'Logout berhasil!');

        } catch (\Exception $e) {
            \Log::error('Logout error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat logout.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Terjadi kesalahan saat logout.');
        }
    }

    /**
     * Get current user
     */
    public function getUser(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'message' => 'Success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'is_active' => $user->is_active,
                    'last_login_at' => $user->last_login_at,
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Get user error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}