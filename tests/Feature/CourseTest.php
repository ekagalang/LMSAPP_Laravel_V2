<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
}
