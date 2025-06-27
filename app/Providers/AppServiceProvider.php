<?php

namespace App\Providers;

use App\Models\Course; // Tambahkan ini jika belum ada
use App\Policies\CoursePolicy; // Tambahkan ini jika belum ada
use App\Models\Quiz; // <--- PASTIKAN INI ADA
use App\Policies\QuizPolicy; // <--- PASTIKAN INI ADA
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Course::class => CoursePolicy::class, // Ini sudah ada
        Quiz::class => QuizPolicy::class, // <--- TAMBAHKAN ATAU PASTIKAN INI ADA
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate untuk mengelola kursus
        Gate::define('manage-courses', function (User $user) {
            return $user->isAdmin() || $user->isInstructor();
        });

        // Gate untuk user admin
        Gate::define('admin-only', function (User $user) {
            return $user->isAdmin();
        });
    }
}