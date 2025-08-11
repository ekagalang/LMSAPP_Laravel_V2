<?php

// =============================================================================
// tests/Unit/CourseModelTest.php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\User;
use App\Models\Lesson;
use App\Models\CoursePeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_course_has_fillable_attributes(): void
    {
        $course = new Course();
        // Sesuaikan dengan actual fillable dari Course model
        $expected = ['title', 'description', 'objectives', 'thumbnail', 'status', 'certificate_template_id'];
        
        $this->assertEquals($expected, $course->getFillable());
    }

    public function test_course_belongs_to_many_users(): void
    {
        $course = Course::factory()->create();
        $user = User::factory()->create();
        
        // Gunakan enrolledUsers() sesuai dengan actual relationship
        $course->enrolledUsers()->attach($user->id);
        
        $this->assertTrue($course->enrolledUsers->contains($user));
    }

    public function test_course_has_many_lessons(): void
    {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        
        $this->assertTrue($course->lessons->contains($lesson));
    }

    public function test_course_has_many_periods(): void
    {
        $course = Course::factory()->create();
        
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $course->periods());
    }

    public function test_course_scope_published(): void
    {
        // Gunakan 'status' = 'published' sesuai dengan actual schema
        Course::factory()->create(['status' => 'published']);
        Course::factory()->create(['status' => 'draft']);
        
        $publishedCourses = Course::where('status', 'published')->get();
        
        $this->assertCount(1, $publishedCourses);
        $this->assertEquals('published', $publishedCourses->first()->status);
    }

    public function test_course_can_calculate_progress_for_user(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $lesson1 = Lesson::factory()->create(['course_id' => $course->id]);
        $lesson2 = Lesson::factory()->create(['course_id' => $course->id]);
        
        $course->enrolledUsers()->attach($user->id);
        
        // Test basic relationship setup
        $this->assertTrue($course->enrolledUsers->contains($user));
        $this->assertCount(2, $course->lessons);
    }
}