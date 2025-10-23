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
        // Super-admin handled by Gate::before in AuthServiceProvider
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
        // Users with full course management can start any quiz (e.g., reviewers)
        if ($user->can('manage all courses')) {
            return true;
        }

        // Pastikan kuis terhubung ke kursus
        if (!$quiz->lesson || !$quiz->lesson->course) {
            return false;
        }

        $course = $quiz->lesson->course;

        // Instructors (by relation or quiz owner) may preview
        if ($user->can('view quizzes')) {
            return $quiz->user_id === $user->id ||
                   $course->instructors()->where('user_id', $user->id)->exists();
        }

        // Izinkan peserta jika terdaftar di kursus
        return $user->courses()->where('course_id', $course->id)->exists();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view quizzes');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quiz $quiz): bool
    {
        // Pastikan kuis terhubung ke kursus
        if (!$quiz->lesson || !$quiz->lesson->course) {
            return false;
        }

        $course = $quiz->lesson->course;

        // Instructors (by permission) can view if owner or assigned to course
        if ($user->can('view quizzes')) {
            return $quiz->user_id === $user->id || 
                   $course->instructors()->where('user_id', $user->id)->exists();
        }

        // Participants can view if allowed to attempt and enrolled in course
        if ($user->can('attempt quizzes')) {
            return $this->start($user, $quiz);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create quizzes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quiz $quiz): bool
    {
        // Pastikan kuis terhubung ke kursus
        if (!$quiz->lesson || !$quiz->lesson->course) {
            return $user->can('update quizzes') && $quiz->user_id === $user->id;
        }

        $course = $quiz->lesson->course;

        // Update if permitted and either owner or assigned instructor
        if ($user->can('manage all courses')) {
            return true;
        }
        return $user->can('update quizzes') && 
               ($quiz->user_id === $user->id || 
                $course->instructors()->where('user_id', $user->id)->exists());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        // Pastikan kuis terhubung ke kursus
        if (!$quiz->lesson || !$quiz->lesson->course) {
            return $user->can('delete quizzes') && $quiz->user_id === $user->id;
        }

        $course = $quiz->lesson->course;

        if ($user->can('manage all courses')) {
            return true;
        }
        return $user->can('delete quizzes') && 
               ($quiz->user_id === $user->id || 
                $course->instructors()->where('user_id', $user->id)->exists());
    }

    public function attempt(User $user, Quiz $quiz)
    {
        // Pastikan kuis terhubung ke kursus
        if (!$quiz->lesson || !$quiz->lesson->course) {
            return false;
        }

        $course = $quiz->lesson->course;

        // Allow if user has permission and is enrolled
        return $user->can('attempt quizzes') && 
               $user->courses()->where('course_id', $course->id)->exists();
}
}
