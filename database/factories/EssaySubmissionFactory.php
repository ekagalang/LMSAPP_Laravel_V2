<?php

namespace Database\Factories;

use App\Models\EssaySubmission;
use App\Models\User;
use App\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;

class EssaySubmissionFactory extends Factory
{
    protected $model = EssaySubmission::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content_id' => Content::factory(),
            'submitted_at' => $this->faker->dateTimeThisMonth(),
            'score' => null,
            'feedback' => null,
        ];
    }

    public function graded(): static
    {
        return $this->state([
            'score' => $this->faker->numberBetween(60, 100),
            'feedback' => $this->faker->paragraph,
        ]);
    }
}