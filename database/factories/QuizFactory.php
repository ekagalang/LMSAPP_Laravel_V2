<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition(): array
    {
        return [
            'content_id' => Content::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'total_marks' => 100,
            'pass_marks' => 60,
            'time_limit' => $this->faker->numberBetween(10, 120),
            'show_answers_after_attempt' => $this->faker->boolean,
            'status' => $this->faker->randomElement(['draft', 'published']),
        ];
    }
}