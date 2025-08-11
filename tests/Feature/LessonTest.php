<?php

namespace Tests\Feature;

use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_lesson_can_be_created(): void
    {
        $lesson = Lesson::factory()->create();

        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'title' => $lesson->title,
        ]);
    }

    public function test_lesson_can_be_updated(): void
    {
        $lesson = Lesson::factory()->create();

        $lesson->update(['title' => 'Updated Title']);

        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_lesson_can_be_deleted(): void
    {
        $lesson = Lesson::factory()->create();

        $lesson->delete();

        $this->assertModelMissing($lesson);
    }
}
