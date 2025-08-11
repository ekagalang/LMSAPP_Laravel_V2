<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Content;
use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_instructor_can_create_quiz(): void
    {
        $instructor = $this->createInstructorUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        $course->instructors()->attach($instructor->id);

        $quizData = [
            'lesson_id' => $lesson->id,
            'title' => 'Test Quiz',
            'description' => 'Test Description',
            'total_marks' => 100,
            'pass_marks' => 60,
            'time_limit' => 30,
            'status' => 'published',
            'questions' => [
                [
                    'question_text' => 'What is 2+2?',
                    'points' => 10,
                    'options' => [
                        ['text' => '3', 'is_correct' => false],
                        ['text' => '4', 'is_correct' => true],
                        ['text' => '5', 'is_correct' => false],
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($instructor)->post(route('quizzes.store'), $quizData);

        $response->assertRedirect();
        $this->assertDatabaseHas('quizzes', [
            'title' => 'Test Quiz',
            'total_marks' => 100,
            'pass_marks' => 60,
        ]);
    }

    public function test_student_can_start_quiz_attempt(): void
    {
        $student = $this->createParticipantUser();
        $instructor = $this->createInstructorUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        $content = Content::factory()->create(['lesson_id' => $lesson->id, 'type' => 'quiz']);
        $quiz = Quiz::factory()->create(['content_id' => $content->id, 'time_limit' => 30]);

        $course->enrolledUsers()->attach($student->id);
        $course->instructors()->attach($instructor->id);

        $response = $this->actingAs($student)->post(route('quizzes.start_attempt', $quiz));

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'completed' => false,
        ]);
    }

    public function test_quiz_attempt_calculates_score_correctly(): void
    {
        $student = $this->createParticipantUser();
        $quiz = $this->createQuizWithQuestions();
        
        $attempt = QuizAttempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
        ]);

        $questions = $quiz->questions;
        $correctAnswer = $questions->first()->options->where('is_correct', true)->first();
        $wrongAnswer = $questions->get(1)->options->where('is_correct', false)->first();

        // Answer first question correctly, second incorrectly
        QuizAttemptAnswer::factory()->create([
            'quiz_attempt_id' => $attempt->id,
            'question_id' => $questions->first()->id,
            'option_id' => $correctAnswer->id,
        ]);

        QuizAttemptAnswer::factory()->create([
            'quiz_attempt_id' => $attempt->id,
            'question_id' => $questions->get(1)->id,
            'option_id' => $wrongAnswer->id,
        ]);

        $response = $this->actingAs($student)->post(
            route('quizzes.submit_attempt', [$quiz, $attempt])
        );

        $response->assertRedirect();
        
        $attempt->refresh();
        $this->assertTrue($attempt->completed);
        $this->assertEquals(50, $attempt->score); // 1 out of 2 correct = 50%
    }

    public function test_quiz_time_limit_enforced(): void
    {
        $student = $this->createParticipantUser();
        $quiz = Quiz::factory()->create(['time_limit' => 1]); // 1 minute

        $attempt = QuizAttempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'started_at' => now()->subMinutes(2), // Started 2 minutes ago
        ]);

        $response = $this->actingAs($student)->get(
            route('quizzes.check_time', [$quiz, $attempt])
        );

        $response->assertStatus(200);
        $response->assertJson(['time_up' => true]);
    }

    private function createQuizWithQuestions()
    {
        $quiz = Quiz::factory()->create();
        
        // Create 2 questions worth 50 points each
        $question1 = Question::factory()->create(['quiz_id' => $quiz->id, 'points' => 50]);
        $question2 = Question::factory()->create(['quiz_id' => $quiz->id, 'points' => 50]);

        // Create options for each question
        Option::factory()->create(['question_id' => $question1->id, 'is_correct' => true]);
        Option::factory()->create(['question_id' => $question1->id, 'is_correct' => false]);
        
        Option::factory()->create(['question_id' => $question2->id, 'is_correct' => true]);
        Option::factory()->create(['question_id' => $question2->id, 'is_correct' => false]);

        return $quiz->fresh(['questions.options']);
    }
}