<?php
// app/Policies/ChatPolicy.php - FIXED VERSION

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChatPolicy
{
    public function view(User $user, Chat $chat): bool
    {
        // User must be a participant in the chat
        if (!$chat->hasParticipant($user->id)) {
            return false;
        }

        // Chat must be active
        if (!$chat->is_active) {
            return false;
        }

        // If chat is associated with a course period, it must be active
        if ($chat->courseClass && !$chat->courseClass->isActive()) {
            // Only users with full course management can view inactive course chats
            return $user->can('manage all courses');
        }

        return true;
    }

    public function create(User $user): bool
    {
        if ($user->can('manage all courses')) {
            return true;
        }
        return $user->can('create chats');
    }

    public function sendMessage(User $user, Chat $chat): bool
    {
        if (!$this->view($user, $chat)) {
            return false;
        }
        if ($chat->courseClass && !$chat->courseClass->isActive()) {
            return false;
        }
        return $user->can('send chat messages') || $user->can('manage all courses');
    }

    public function addParticipant(User $user, Chat $chat): bool
    {
        // Managers can always add participants
        if ($user->can('manage all courses')) {
            return true;
        }

        // Chat creator can add participants if allowed
        if ($chat->created_by === $user->id && $user->can('add chat participants')) {
            return true;
        }

        // Instructors/organizers tied to the course can add (needs permission)
        if ($chat->courseClass && $user->can('add chat participants')) {
            $course = $chat->courseClass->course;
            return $course->instructors->contains($user->id) || $course->eventOrganizers->contains($user->id);
        }

        return false;
    }

    public function removeParticipant(User $user, Chat $chat): bool
    {
        // Managers can always remove participants
        if ($user->can('manage all courses')) {
            return true;
        }

        // Chat creator can remove participants if allowed
        if ($chat->created_by === $user->id && $user->can('remove chat participants')) {
            return true;
        }

        // Instructors/organizers tied to the course can remove (needs permission)
        if ($chat->courseClass && $user->can('remove chat participants')) {
            $course = $chat->courseClass->course;
            return $course->instructors->contains($user->id) || $course->eventOrganizers->contains($user->id);
        }

        return false;
    }

    /**
     * ✅ FIXED: Check if user can create chat for specific course period
     */
    public function createForCourseClass(User $user, $courseClassId = null): bool
    {
        // If no course class specified, use general create permission
        if (!$courseClassId) {
            return $this->create($user);
        }

        $courseClass = \App\Models\CourseClass::find($courseClassId);
        if (!$courseClass) {
            return false;
        }

        // Managers can create chat for any course
        if ($user->can('manage all courses')) {
            return true;
        }

        // Allow instructors/EOs tied to the course (requires permission)
        if ($user->can('create course chats')) {
            $isInstructor = $courseClass->course->instructors()->where('user_id', $user->id)->exists();
            if ($isInstructor) {
                return true;
            }
            $isEventOrganizer = $courseClass->course->eventOrganizers()->where('user_id', $user->id)->exists();
            if ($isEventOrganizer) {
                return true;
            }
        }

        // ✅ FIXED: Check if user is participant in this course period
        // This covers students and other participants
        $isParticipant = $courseClass->participants()->where('user_id', $user->id)->exists();
        if ($isParticipant) {
            return true;
        }

        return false;
    }
}
