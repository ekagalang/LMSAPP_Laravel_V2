<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'option_text' => $this->faker->sentence,
            'is_correct' => false,
        ];
    }

    public function correct(): static
    {
        return $this->state(['is_correct' => true]);
    }
}