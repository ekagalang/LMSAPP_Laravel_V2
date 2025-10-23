<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class PermissionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_permissions_exist_after_seeding(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $expected = [
            'create chats',
            'send chat messages',
            'add chat participants',
            'remove chat participants',
            'create course chats',
        ];

        foreach ($expected as $name) {
            $this->assertNotNull(Permission::where('name', $name)->first(), "Missing permission: {$name}");
        }
    }
}

