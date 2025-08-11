<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_can_be_created(): void
    {
        $course = Course::factory()->create();
        $instructor = User::factory()->create();

        $course->instructors()->attach($instructor->id);

        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_user_can_enroll_in_course(): void
    {
        $instructor = User::factory()->create();
        $course = Course::factory()->create(['status' => 'published']);
        $course->instructors()->attach($instructor->id);
        $user = User::factory()->create();

        $course->enrolledUsers()->attach($user->id);

        $this->assertTrue($course->refresh()->enrolledUsers->contains($user));
    }

    public function test_enrolled_user_can_access_course_content(): void
    {
        $instructor = User::factory()->create();
        $user = User::factory()->create();
        $course = Course::factory()->create(['status' => 'published']);
        $course->instructors()->attach($instructor->id);
        $lesson = Lesson::factory()->for($course)->create(['order' => 1]);
        $content = Content::factory()->for($lesson)->create(['order' => 1]);

        $course->enrolledUsers()->attach($user->id);

        $response = $this->actingAs($user)->get(route('contents.show', $content));

        $response->assertOk();
    }

    public function test_course_can_be_updated_via_endpoint(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::create(['name' => 'manage all courses']);
        $user = User::factory()->create();
        $user->givePermissionTo('manage all courses');

        $course = Course::factory()->create();

        $data = [
            'title' => 'Updated Course',
            'description' => 'Updated description',
            'objectives' => 'Updated objectives',
            'status' => 'published',
        ];

        $response = $this->actingAs($user)->patch(route('courses.update', $course), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', array_merge(['id' => $course->id], $data));
    }

    public function test_course_can_be_deleted_via_endpoint(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::create(['name' => 'manage all courses']);
        $user = User::factory()->create();
        $user->givePermissionTo('manage all courses');

        $course = Course::factory()->create();

        $response = $this->actingAs($user)->delete(route('courses.destroy', $course));

        $response->assertRedirect();
        $this->assertModelMissing($course);
    }
}
