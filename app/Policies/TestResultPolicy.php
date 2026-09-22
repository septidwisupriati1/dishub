<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TestResult;

class TestResultPolicy
{
    /**
     * Siapa yang boleh melihat list
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['penguji', 'admin']);
    }

    /**
     * Siapa yang boleh lihat detail
     */
    public function view(User $user, TestResult $testResult): bool
    {
        return in_array($user->role, ['penguji', 'admin']);
    }

    /**
     * Siapa yang boleh create
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['penguji', 'admin']);
    }
}