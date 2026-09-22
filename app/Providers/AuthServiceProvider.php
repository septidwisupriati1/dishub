<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Queue;
use App\Models\TestResult;
use App\Policies\VehiclePolicy;
use App\Policies\QueuePolicy;
use App\Policies\TestResultPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Vehicle::class => VehiclePolicy::class,
        Queue::class => QueuePolicy::class,
        TestResult::class => TestResultPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate untuk custom permission
        \Illuminate\Support\Facades\Gate::define('create-vehicle', function (User $user) {
            return $user->isPeserta();
        });

        \Illuminate\Support\Facades\Gate::define('update-vehicle', function (User $user, Vehicle $vehicle) {
            return ($user->isPeserta() && $user->id === $vehicle->user_id) || $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('delete-vehicle', function (User $user, Vehicle $vehicle) {
            return ($user->isPeserta() && $user->id === $vehicle->user_id) || $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('create-queue', function (User $user) {
            return $user->isPeserta();
        });

        \Illuminate\Support\Facades\Gate::define('update-queue', function (User $user, Queue $queue) {
            return $user->isPenguji() || $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('delete-queue', function (User $user, Queue $queue) {
            return $user->isPenguji() || $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('call-queue', function (User $user, Queue $queue) {
            return $user->isPenguji() || $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('cancel-queue', function (User $user, Queue $queue) {
            return ($user->isPeserta() && $user->id === $queue->vehicle->user_id) || $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('create-test-result', function (User $user) {
            return $user->isPenguji() || $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('view-penguji-stats', function (User $user) {
            return $user->isPenguji() || $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('view-admin-report', function (User $user) {
            return $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('manage-test-schedules', function (User $user) {
            return $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('manage-whatsapp-config', function (User $user) {
            return $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });
    }
}