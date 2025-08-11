<?php

namespace Database\Factories;

use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizAttemptFactory extends Factory
{
    protected $model = QuizAttempt::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'quiz_id' => Quiz::factory(),
            'started_at' => $this->faker->dateTimeThisMonth(),
            'completed_at' => null,
            'score' => null,
            'passed' => false,
            'completed' => false,
        ];
    }

    public function completed(): static
    {
        return $this->state([
            'completed_at' => $this->faker->dateTimeThisMonth(),
            'score' => $this->faker->numberBetween(0, 100),
            'passed' => $this->faker->boolean(70),
            'completed' => true,
        ]);
    }
}