<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'peserta';
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        // Peserta boleh update kendaraan sendiri, admin bisa update semua
        return ($user->role === 'peserta' && $user->id === $vehicle->user_id) || $user->role === 'admin';
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        // Peserta boleh hapus kendaraan sendiri, admin bisa hapus semua
        return ($user->role === 'peserta' && $user->id === $vehicle->user_id) || $user->role === 'admin';
    }
}