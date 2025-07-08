<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuizPolicy
{
    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @return bool|null
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole(['super-admin', 'admin'])) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can start the quiz.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quiz  $quiz
     * @return bool
     */
    public function start(User $user, Quiz $quiz): bool
    {
        // Izinkan instruktur untuk memulai kuisnya sendiri
        if ($user->hasRole('instructor')) {
            return $quiz->user_id === $user->id;
        }

        // Pastikan kuis terhubung ke kursus
        if (!$quiz->lesson || !$quiz->lesson->course) {
            return false;
        }

        // Izinkan peserta jika terdaftar di kursus
        $course = $quiz->lesson->course;
        return $user->courses()->where('course_id', $course->id)->exists();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['instructor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quiz $quiz): bool
    {
        // Jika pengguna adalah instruktur, dia hanya bisa melihat kuisnya sendiri.
        if ($user->hasRole('instructor')) {
            return $quiz->user_id === $user->id;
        }

        // Peserta bisa melihat kuis jika terdaftar di kursus.
        if($user->hasRole('participant')){
            return $this->start($user, $quiz);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['instructor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quiz $quiz): bool
    {
        return $user->hasRole('instructor') && $quiz->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        return $user->hasRole('instructor') && $quiz->user_id === $user->id;
    }
}