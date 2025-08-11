<?php

namespace Database\Factories;

use App\Models\EssayQuestion;
use App\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;

class EssayQuestionFactory extends Factory
{
    protected $model = EssayQuestion::class;

    public function definition(): array
    {
        return [
            'content_id' => Content::factory(),
            'question' => $this->faker->sentence . '?',
            'order' => $this->faker->numberBetween(1, 10),
            'max_score' => $this->faker->numberBetween(10, 100),
        ];
    }
}