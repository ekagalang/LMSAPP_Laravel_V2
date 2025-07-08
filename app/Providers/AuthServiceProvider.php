<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Quiz;
use App\Policies\CoursePolicy;
use App\Policies\QuizPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // PERBAIKAN: Hapus tanda komentar untuk mendaftarkan CoursePolicy.
        // Ini akan memberitahu Laravel untuk menggunakan aturan di CoursePolicy
        // setiap kali ada pengecekan hak akses terkait model Course.
        Course::class => CoursePolicy::class,
        Quiz::class => QuizPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Metode ini akan dijalankan sebelum semua pengecekan Gate & Policy lainnya.
        // Jika user memiliki peran 'super-admin', maka akan selalu return true,
        // yang memberinya akses ke semua fitur.
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
