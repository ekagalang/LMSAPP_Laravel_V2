<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_bypass_unlock_check_with_permissions(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $instructor = User::factory()->create();
        $instructor->givePermissionTo(['manage own courses', 'update contents']);

        $course = Course::factory()->create(['status' => 'published']);
        $course->instructors()->attach($instructor->id);

        $lesson = Lesson::factory()->create(['course_id' => $course->id, 'order' => 1]);
        $content = Content::factory()->create(['lesson_id' => $lesson->id, 'order' => 1]);

        $this->actingAs($instructor);
        $response = $this->get(route('contents.show', ['content' => $content->id]));
        $response->assertStatus(200);
    }
}

