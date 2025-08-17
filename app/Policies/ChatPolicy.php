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
        if ($chat->coursePeriod && !$chat->coursePeriod->isActive()) {
            // Only admin can view inactive course chats
            return $user->hasRole('super-admin');
        }

        return true;
    }

    public function create(User $user): bool
    {
        // Admin can always create chats
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Instructors and event organizers can create chats
        if ($user->hasRole(['instructor', 'event-organizer'])) {
            return true;
        }

        // Participants can create direct chats but may be restricted for group chats
        // This can be further customized based on business rules
        if ($user->hasRole('participant')) {
            return true; // Allow for now, can be restricted later
        }

        return false;
    }

    public function sendMessage(User $user, Chat $chat): bool
    {
        // Must be able to view the chat first
        if (!$this->view($user, $chat)) {
            return false;
        }

        // Admin can always send messages
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // If chat has a course period, it must be active
        if ($chat->coursePeriod && !$chat->coursePeriod->isActive()) {
            return false;
        }

        return true;
    }

    public function addParticipant(User $user, Chat $chat): bool
    {
        // Admin can always add participants
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Chat creator can add participants
        if ($chat->created_by === $user->id) {
            return true;
        }

        // Instructors can add participants to course chats where they are instructors
        if ($user->hasRole('instructor') && $chat->coursePeriod) {
            return $chat->coursePeriod->course->instructors->contains($user->id);
        }

        // Event organizers can add participants to course chats where they are organizers
        if ($user->hasRole('event-organizer') && $chat->coursePeriod) {
            return $chat->coursePeriod->course->eventOrganizers->contains($user->id);
        }

        return false;
    }

    public function removeParticipant(User $user, Chat $chat): bool
    {
        // Admin can always remove participants
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Chat creator can remove participants
        if ($chat->created_by === $user->id) {
            return true;
        }

        // Instructors can remove participants from course chats where they are instructors
        if ($user->hasRole('instructor') && $chat->coursePeriod) {
            return $chat->coursePeriod->course->instructors->contains($user->id);
        }

        // Event organizers can remove participants from course chats where they are organizers
        if ($user->hasRole('event-organizer') && $chat->coursePeriod) {
            return $chat->coursePeriod->course->eventOrganizers->contains($user->id);
        }

        return false;
    }

    /**
     * ✅ FIXED: Check if user can create chat for specific course period
     */
    public function createForCoursePeriod(User $user, $coursePeriodId = null): bool
    {
        // If no course period specified, use general create permission
        if (!$coursePeriodId) {
            return $this->create($user);
        }

        $coursePeriod = \App\Models\CoursePeriod::find($coursePeriodId);
        if (!$coursePeriod) {
            return false;
        }

        // Admin can create chat for any course
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // ✅ FIXED: Allow instructors to create chat in their courses
        if ($user->hasRole('instructor')) {
            // Check if user is instructor of this course
            $isInstructor = $coursePeriod->course->instructors()->where('user_id', $user->id)->exists();
            if ($isInstructor) {
                return true;
            }
        }

        // ✅ FIXED: Allow event organizers to create chat in their courses  
        if ($user->hasRole('event-organizer')) {
            // Check if user is event organizer of this course
            $isEventOrganizer = $coursePeriod->course->eventOrganizers()->where('user_id', $user->id)->exists();
            if ($isEventOrganizer) {
                return true;
            }
        }

        // ✅ FIXED: Check if user is participant in this course period
        // This covers students and other participants
        $isParticipant = $coursePeriod->participants()->where('user_id', $user->id)->exists();
        if ($isParticipant) {
            return true;
        }

        return false;
    }
}
