<?php
// =============================================================================
// 1. PERBAIKAN: tests/Unit/UserModelTest.php
// =============================================================================

namespace Tests\Unit;

use App\Models\User;
use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_user_has_fillable_attributes(): void
    {
        $user = new User();
        // Sesuaikan dengan actual fillable attributes dari User model
        $expected = ['name', 'email', 'password', 'role'];
        
        $this->assertEquals($expected, $user->getFillable());
    }

    public function test_user_has_hidden_attributes(): void
    {
        $user = new User();
        $expected = ['password', 'remember_token'];
        
        $this->assertEquals($expected, $user->getHidden());
    }

    public function test_user_can_be_assigned_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('participant');
        
        $this->assertTrue($user->hasRole('participant'));
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole(['participant', 'instructor']);

        $this->assertTrue($user->hasRole('participant'));
        $this->assertTrue($user->hasRole('instructor'));
        $this->assertCount(2, $user->roles);
    }

    public function test_user_can_enroll_in_courses(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $user->courses()->attach($course->id);

        $this->assertTrue($user->courses->contains($course));
    }

    public function test_user_certificates_relationship(): void
    {
        $user = User::factory()->create();
        
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $user->certificates());
    }

    public function test_user_email_verification(): void
    {
        $user = User::factory()->unverified()->create();
        
        $this->assertNull($user->email_verified_at);
        $this->assertFalse($user->hasVerifiedEmail());
        
        $user->markEmailAsVerified();
        
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_user_primary_role_attribute(): void
    {
        $user = User::factory()->create();
        $user->assignRole('instructor');

        $this->assertEquals('instructor', $user->primary_role);
    }

    public function test_user_courses_relationship(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        
        $user->courses()->attach($course->id);
        
        $this->assertTrue($user->courses->contains($course));
    }
}