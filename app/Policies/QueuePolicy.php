<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Queue;

class QueuePolicy
{
    /**
     * Tentukan apakah user bisa create queue
     */
    public function create(User $user): bool
    {
        return $user->isPeserta();
    }

    /**
     * Tentukan apakah user bisa call queue
     */
    public function callQueue(User $user, Queue $queue): bool
    {
        return $user->isPenguji() || $user->isAdmin();
    }

    /**
     * Tentukan apakah user bisa cancel queue
     */
    public function cancelQueue(User $user, Queue $queue): bool
    {
        return $user->id === $queue->user_id || $user->isAdmin();
    }
}