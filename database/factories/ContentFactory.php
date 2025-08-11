<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    protected $model = Content::class;

    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'title' => $this->faker->sentence(),
            'type' => 'text',
            'body' => $this->faker->paragraph(),
            'file_path' => null,
            'order' => 1,
        ];
    }
}
