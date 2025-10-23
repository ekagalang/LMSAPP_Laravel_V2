<?php

namespace App\Policies;

use App\Models\Discussion;
use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DiscussionPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->can('manage all courses')) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view discussions for a course.
     */
    public function viewCourseDiscussions(User $user, Course $course): bool
    {
        // Allow if user can manage all courses (Event Organizers, Admins)
        if ($user->can('manage all courses')) {
            return true;
        }

        // Allow if user can manage own courses AND is instructor of this course
        if ($user->can('manage own courses') && $course->instructors->contains($user)) {
            return true;
        }

        // Allow if user has manage discussions permission
        if ($user->can('manage discussions')) {
            return true;
        }

        // Allow enrolled participants to view discussions
        if ($course->enrolledUsers->contains($user)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create discussions.
     */
    public function create(User $user, Course $course): bool
    {
        // Must be enrolled in the course or be an instructor
        return $course->enrolledUsers->contains($user) ||
            $course->instructors->contains($user) ||
            $user->can('manage all courses');
    }

    /**
     * Determine whether the user can view a specific discussion.
     */
    public function view(User $user, Discussion $discussion): bool
    {
        $course = $discussion->content->lesson->course;
        return $this->viewCourseDiscussions($user, $course);
    }

    /**
     * Determine whether the user can update a discussion.
     */
    public function update(User $user, Discussion $discussion): bool
    {
        // User can update their own discussions
        if ($discussion->user_id === $user->id) {
            return true;
        }

        // Instructors can moderate discussions in their courses
        $course = $discussion->content->lesson->course;
        return $course->instructors->contains($user) || $user->can('manage all courses');
    }

    /**
     * Determine whether the user can delete a discussion.
     */
    public function delete(User $user, Discussion $discussion): bool
    {
        // Same logic as update
        return $this->update($user, $discussion);
    }

    /**
     * Determine whether the user can reply to a discussion.
     */
    public function reply(User $user, Discussion $discussion): bool
    {
        $course = $discussion->content->lesson->course;

        // Must be enrolled or be an instructor
        return $course->enrolledUsers->contains($user) ||
            $course->instructors->contains($user) ||
            $user->can('manage all courses');
    }
}
