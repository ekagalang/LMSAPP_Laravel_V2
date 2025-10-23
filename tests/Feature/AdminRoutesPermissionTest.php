<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_users_route_requires_manage_users_permission(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get('/admin/users')->assertForbidden();

        $user->givePermissionTo('manage users');
        $this->get('/admin/users')->assertOk();
    }
}

