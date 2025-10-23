<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\User;
use App\Notifications\ChatMessageNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ChatNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sending_message_notifies_other_participants(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        Notification::fake();

        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        // Permissions to send messages
        $sender->givePermissionTo('send chat messages');
        $recipient->givePermissionTo('send chat messages');

        $chat = Chat::create([
            'type' => 'direct',
            'created_by' => $sender->id,
            'is_active' => true,
        ]);

        $chat->participants()->attach([$sender->id => ['joined_at' => now()], $recipient->id => ['joined_at' => now()]]);

        $this->actingAs($sender);
        $response = $this->postJson(route('messages.store', ['chat' => $chat->id]), [
            'content' => 'Hello there',
            'type' => 'text'
        ]);

        $response->assertStatus(201);

        Notification::assertSentTo($recipient, ChatMessageNotification::class);
        Notification::assertNotSentTo($sender, ChatMessageNotification::class);
    }
}
