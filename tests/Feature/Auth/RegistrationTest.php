<?php
// =============================================================================
// PERBAIKAN 1: tests/Feature/Auth/RegistrationTest.php
// =============================================================================

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles and permissions untuk testing
        $this->seedRolesAndPermissions();
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Get the created user
        $user = User::where('email', 'test@example.com')->first();
        
        // Verify user exists
        $this->assertNotNull($user);
        
        // Verify user is authenticated after registration
        $this->assertAuthenticatedAs($user);
        
        // Verify user has the correct role
        $this->assertTrue($user->hasRole('participant'));
        
        // Verify redirect to dashboard
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        // Create a user first
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_registration_requires_all_fields(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }
}