<?php

// =============================================================================
// tests/Feature/CourseControllerTest.php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_course_index_displays_courses(): void
    {
        $user = $this->createParticipantUser();
        Course::factory()->count(3)->create(['status' => 'published']);

        $response = $this->actingAs($user)->get(route('courses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('courses.index');
        $response->assertViewHas('courses');
    }

    public function test_course_show_displays_course_details(): void
    {
        $user = $this->createParticipantUser();
        $course = Course::factory()->create(['status' => 'published']);

        $response = $this->actingAs($user)->get(route('courses.show', $course));

        $response->assertStatus(200);
        $response->assertViewIs('courses.show');
        $response->assertViewHas('course');
    }

    public function test_authorized_user_can_create_course(): void
    {
        $user = $this->createAdminUser();

        $courseData = [
            'title' => 'Test Course',
            'description' => 'Test Description',
            'objectives' => 'Test Objectives',
            'status' => 'published', // Gunakan 'status' bukan 'is_published'
        ];

        $response = $this->actingAs($user)->post(route('courses.store'), $courseData);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', ['title' => 'Test Course', 'status' => 'published']);
    }

    public function test_unauthorized_user_cannot_create_course(): void
    {
        $user = $this->createParticipantUser();

        $courseData = [
            'title' => 'Test Course',
            'description' => 'Test Description',
            'objectives' => 'Test Objectives',
            'status' => 'published', // Tambahkan field yang required
        ];

        $response = $this->actingAs($user)->post(route('courses.store'), $courseData);

        $response->assertStatus(403);
    }

    public function test_user_can_enroll_in_course(): void
    {
        $instructor = $this->createInstructorUser();
        $user = $this->createParticipantUser();
        $course = Course::factory()->create(['status' => 'published']);

        $response = $this->actingAs($instructor)->post(route('courses.enroll', $course), [
            'user_id' => $user->id,
        ]);

        $response->assertRedirect();
        $this->assertTrue($course->enrolledUsers->contains($user));
    }

    public function test_course_progress_display(): void
    {
        $user = $this->createParticipantUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        
        $course->enrolledUsers()->attach($user->id);

        $response = $this->actingAs($user)->get(route('courses.progress', $course));

        $response->assertStatus(200);
        $response->assertViewIs('courses.progress');
        $response->assertViewHas(['course']);
    }
}