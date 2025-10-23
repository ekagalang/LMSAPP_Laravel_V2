<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardCapabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_sees_participant_dashboard(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->givePermissionTo('attempt quizzes');

        $this->actingAs($user);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Peserta', false);
    }

    public function test_instructor_sees_instructor_dashboard(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $user->givePermissionTo('manage own courses');

        $this->actingAs($user);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Instruktur', false);
    }

    public function test_admin_like_user_sees_admin_dashboard(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $user->givePermissionTo('manage users');

        $this->actingAs($user);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        // Admin dashboard cards are localized; check for Indonesian label
        $response->assertSee('Total Pengguna', false);
    }
}
