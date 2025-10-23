<?php

namespace App\Notifications;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ChatCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Chat $chat, public User $creator)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'chat_id' => $this->chat->id,
            'chat_name' => $this->chat->getDisplayName(),
            'creator_id' => $this->creator->id,
            'creator_name' => $this->creator->name,
            'created_at' => $this->chat->created_at?->toISOString(),
        ];
    }
}

