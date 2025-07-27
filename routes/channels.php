<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = Chat::find($chatId);

    if (!$chat) {
        return false;
    }

    // Check if user is a participant in this chat
    return $chat && $chat->hasParticipant($user->id);
});
