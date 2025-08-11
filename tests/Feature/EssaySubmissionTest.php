<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Content;
use App\Models\EssayQuestion;
use App\Models\EssaySubmission;
use App\Models\EssayAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EssaySubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_student_can_submit_essay(): void
    {
        $student = $this->createParticipantUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        $content = Content::factory()->create(['lesson_id' => $lesson->id, 'type' => 'essay']);
        
        $essayQuestion = EssayQuestion::factory()->create([
            'content_id' => $content->id,
            'question' => 'Explain the importance of testing in software development.',
            'max_score' => 100,
        ]);

        $course->enrolledUsers()->attach($student->id);

        $response = $this->actingAs($student)->post(route('essays.store', $content), [
            'answers' => [
                $essayQuestion->id => 'Testing is crucial for software quality because...',
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('essay_submissions', [
            'user_id' => $student->id,
            'content_id' => $content->id,
        ]);
        
        $this->assertDatabaseHas('essay_answers', [
            'question_id' => $essayQuestion->id,
            'answer' => 'Testing is crucial for software quality because...',
        ]);
    }

    public function test_instructor_can_grade_essay(): void
    {
        $instructor = $this->createInstructorUser();
        $student = $this->createParticipantUser();
        
        $submission = EssaySubmission::factory()->create(['user_id' => $student->id]);
        $essayAnswer = EssayAnswer::factory()->create(['submission_id' => $submission->id]);

        $response = $this->actingAs($instructor)->post(
            route('gradebook.storeEssayGrade', $submission), 
            [
                'answers' => [
                    $essayAnswer->id => [
                        'score' => 85,
                        'feedback' => 'Excellent work! Well structured arguments.',
                    ]
                ]
            ]
        );

        $response->assertRedirect();
        $essayAnswer->refresh();
        
        $this->assertEquals(85, $essayAnswer->score);
        $this->assertEquals('Excellent work! Well structured arguments.', $essayAnswer->feedback);
    }

    public function test_essay_submission_prevents_duplicate_submission(): void
    {
        $student = $this->createParticipantUser();
        $content = Content::factory()->create(['type' => 'essay']);
        
        // Create existing submission
        EssaySubmission::factory()->create([
            'user_id' => $student->id,
            'content_id' => $content->id,
        ]);

        $response = $this->actingAs($student)->post(route('essays.store', $content), [
            'answers' => [1 => 'New submission attempt']
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        // Should still only have 1 submission
        $this->assertEquals(1, EssaySubmission::where('user_id', $student->id)->count());
    }

    public function test_essay_grade_calculation(): void
    {
        $submission = EssaySubmission::factory()->create();
        
        // Create 3 questions worth different points
        $answer1 = EssayAnswer::factory()->create([
            'submission_id' => $submission->id,
            'score' => 20, // Out of 25 possible
        ]);
        
        $answer2 = EssayAnswer::factory()->create([
            'submission_id' => $submission->id,
            'score' => 30, // Out of 35 possible
        ]);
        
        $answer3 = EssayAnswer::factory()->create([
            'submission_id' => $submission->id,
            'score' => 35, // Out of 40 possible
        ]);

        // Total: 85 out of 100 = 85%
        $totalScore = $submission->answers->sum('score');
        $maxPossibleScore = 100;
        $percentage = ($totalScore / $maxPossibleScore) * 100;

        $this->assertEquals(85, $percentage);
    }
}