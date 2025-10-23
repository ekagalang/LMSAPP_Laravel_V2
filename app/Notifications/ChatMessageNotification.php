<?php

namespace App\Notifications;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Chat $chat, public Message $message)
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
            'message_id' => $this->message->id,
            'message_excerpt' => str($this->message->content)->limit(120)->toString(),
            'sender_id' => $this->message->user_id,
            'sender_name' => $this->message->user?->name,
            'sent_at' => $this->message->created_at?->toISOString(),
        ];
    }
}

