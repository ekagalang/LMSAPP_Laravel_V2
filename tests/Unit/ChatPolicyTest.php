<?php

namespace Tests\Unit;

use App\Models\Chat;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_user_with_create_chats_can_create(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create chats');
        $this->assertTrue($user->can('create', Chat::class));
    }

    public function test_user_requires_course_chat_permission_and_relationship_for_course_class_creation(): void
    {
        $instructor = User::factory()->create();
        $instructor->givePermissionTo(['create course chats']);

        $course = Course::factory()->create();
        // Attach as instructor at course-level (by design the policy checks course->instructors)
        $course->instructors()->attach($instructor->id);

        $class = CourseClass::create([
            'course_id' => $course->id,
            'name' => 'Class A',
            'status' => 'active',
        ]);

        $this->assertTrue($instructor->can('createForCourseClass', [Chat::class, $class->id]));

        $noPermUser = User::factory()->create();
        $course->instructors()->attach($noPermUser->id);
        $this->assertFalse($noPermUser->can('createForCourseClass', [Chat::class, $class->id]));
    }
}

