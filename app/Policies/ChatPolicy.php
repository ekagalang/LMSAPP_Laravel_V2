<?php
// app/Policies/ChatPolicy.php

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
        // Only admin can create new chats
        return $user->hasRole('super-admin');
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
        // Only admin or chat creator can add participants
        return $user->hasRole('super-admin') || $chat->created_by === $user->id;
    }

    public function removeParticipant(User $user, Chat $chat): bool
    {
        // Only admin or chat creator can remove participants
        return $user->hasRole('super-admin') || $chat->created_by === $user->id;
    }
}
