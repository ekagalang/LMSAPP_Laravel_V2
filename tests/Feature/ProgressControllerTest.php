<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Content;
use App\Models\Progress;
use App\Models\ContentProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_student_can_mark_lesson_as_completed(): void
    {
        $student = $this->createParticipantUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $course->enrolledUsers()->attach($student->id);

        $response = $this->actingAs($student)->post(route('lessons.complete', $lesson));

        $response->assertRedirect();
        $this->assertDatabaseHas('progress', [
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'completed' => true,
        ]);
    }

    public function test_content_completion_tracked(): void
    {
        $student = $this->createParticipantUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        $content = Content::factory()->create(['lesson_id' => $lesson->id, 'type' => 'text']);

        $course->enrolledUsers()->attach($student->id);

        $response = $this->actingAs($student)->post(
            route('contents.complete_and_continue', $content)
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('content_progress', [
            'user_id' => $student->id,
            'content_id' => $content->id,
            'completed' => true,
        ]);
    }

    public function test_course_progress_calculation(): void
    {
        $student = $this->createParticipantUser();
        $course = Course::factory()->create();
        
        // Create 4 lessons
        $lessons = Lesson::factory()->count(4)->create(['course_id' => $course->id]);

        $course->enrolledUsers()->attach($student->id);

        // Complete 2 out of 4 lessons
        Progress::factory()->create([
            'user_id' => $student->id,
            'lesson_id' => $lessons[0]->id,
            'completed' => true,
        ]);
        
        Progress::factory()->create([
            'user_id' => $student->id,
            'lesson_id' => $lessons[1]->id,
            'completed' => true,
        ]);

        $response = $this->actingAs($student)->get(route('courses.progress', $course));

        $response->assertStatus(200);
        $response->assertViewHas('progress');
        
        // Should show 50% progress (2/4 lessons completed)
        $progressData = $response->viewData('progress');
        $this->assertEquals(50, $progressData['percentage']);
    }

    public function test_instructor_can_view_all_student_progress(): void
    {
        $instructor = $this->createInstructorUser();
        $students = User::factory()->count(3)->create();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $course->instructors()->attach($instructor->id);
        
        foreach ($students as $index => $student) {
            $student->assignRole('participant');
            $course->enrolledUsers()->attach($student->id);
            
            // Complete lesson for some students
            if ($index < 2) {
                Progress::factory()->create([
                    'user_id' => $student->id,
                    'lesson_id' => $lesson->id,
                    'completed' => true,
                ]);
            }
        }

        $response = $this->actingAs($instructor)->get(route('courses.progress', $course));

        $response->assertStatus(200);
        $response->assertViewHas(['course', 'progressData']);
        
        $progressData = $response->viewData('progressData');
        $this->assertCount(3, $progressData); // Should show all 3 students
    }

    public function test_progress_export_to_pdf(): void
    {
        $instructor = $this->createInstructorUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        $student = $this->createParticipantUser();

        $course->instructors()->attach($instructor->id);
        $course->enrolledUsers()->attach($student->id);
        
        Progress::factory()->create([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'completed' => true,
        ]);

        $response = $this->actingAs($instructor)->get(
            route('courses.exportProgressPdf', $course)
        );

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_prerequisite_enforcement(): void
    {
        $student = $this->createParticipantUser();
        $course = Course::factory()->create();
        
        $lesson1 = Lesson::factory()->create(['course_id' => $course->id, 'order' => 1]);
        $lesson2 = Lesson::factory()->create(['course_id' => $course->id, 'order' => 2]);
        
        $content1 = Content::factory()->create(['lesson_id' => $lesson1->id, 'order' => 1]);
        $content2 = Content::factory()->create(['lesson_id' => $lesson2->id, 'order' => 1]);

        $course->enrolledUsers()->attach($student->id);

        // Try to access lesson 2 content without completing lesson 1
        $response = $this->actingAs($student)->get(route('contents.show', $content2));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
