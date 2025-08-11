<?php

namespace Database\Factories;

use App\Models\Progress;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgressFactory extends Factory
{
    protected $model = Progress::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'completed' => $this->faker->boolean(70),
            'completed_at' => $this->faker->optional()->dateTimeThisMonth(),
        ];
    }

    public function completed(): static
    {
        return $this->state([
            'completed' => true,
            'completed_at' => $this->faker->dateTimeThisMonth(),
        ]);
    }
}