<?php

namespace Database\Factories;

use App\Models\EssayAnswer;
use App\Models\EssaySubmission;
use App\Models\EssayQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class EssayAnswerFactory extends Factory
{
    protected $model = EssayAnswer::class;

    public function definition(): array
    {
        return [
            'submission_id' => EssaySubmission::factory(),
            'question_id' => EssayQuestion::factory(),
            'answer' => $this->faker->paragraphs(3, true),
            'score' => null,
            'feedback' => null,
        ];
    }
}