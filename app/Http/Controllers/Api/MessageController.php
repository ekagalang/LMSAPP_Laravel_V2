<?php
// app/Http/Controllers/Api/MessageController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Events\MessageSent;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ChatMessageNotification;

class MessageController extends Controller
{
    public function index(Request $request, Chat $chat)
    {
        Gate::authorize('view', $chat);

        $request->validate([
            'before' => ['nullable', 'integer', 'exists:messages,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = $chat->messages()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc');

        if ($request->before) {
            $query->olderThan($request->before);
        }

        $messages = $query->limit($request->get('limit', 50))->get();

        // Update last read timestamp
        $chat->participants()->updateExistingPivot($request->user()->id, [
            'last_read_at' => now()
        ]);

        return response()->json([
            'messages' => $messages->reverse()->values()->map(function ($message) {
                return $message->toArray();
            })
        ]);
    }

    public function store(Request $request, Chat $chat)
    {
        Gate::authorize('sendMessage', $chat);

        $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'type' => ['nullable', 'in:text,file'],
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'type' => $request->get('type', 'text'),
        ]);

        $message->load('user:id,name');

        // Update chat's last message timestamp
        $chat->updateLastMessage();

        // Broadcast the message
        broadcast(new MessageSent($message, $chat))->toOthers();

        // Notify other participants (database notification)
        try {
            $recipientIds = $chat->participants()->pluck('users.id')->filter(fn($id) => (int)$id !== (int)$request->user()->id);
            if ($recipientIds->isNotEmpty()) {
                $recipients = \App\Models\User::whereIn('id', $recipientIds)->get();
                if ($recipients->isNotEmpty()) {
                    Notification::send($recipients, new ChatMessageNotification($chat, $message));
                }
            }
        } catch (\Throwable $e) { \Log::warning('Chat message notify error: '.$e->getMessage()); }

        return response()->json(['message' => $message->toArray()], 201);
    }

    public function typing(Request $request, Chat $chat)
    {
        Gate::authorize('view', $chat);

        $request->validate([
            'is_typing' => ['required', 'boolean'],
        ]);

        broadcast(new UserTyping(
            $request->user(),
            $chat,
            $request->boolean('is_typing')
        ))->toOthers();

        return response()->json(['status' => 'ok']);
    }

    public function userTyping(Request $request, Chat $chat)
    {
        // Keamanan tambahan: pastikan user adalah partisipan
        if (! $chat->hasParticipant(auth()->id())) {
             return response()->json(['message' => 'Forbidden'], 403);
        }
        
        // Menyiarkan event ke user lain
        broadcast(new UserTyping(auth()->user(), $chat, true))->toOthers();

        return response()->json(['status' => 'ok']);
    }
}
