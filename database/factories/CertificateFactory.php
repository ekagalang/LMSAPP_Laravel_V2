<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use App\Models\CertificateTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'template_id' => CertificateTemplate::factory(),
            'verification_code' => strtoupper($this->faker->bothify('??####??')),
            'file_path' => 'certificates/' . $this->faker->uuid . '.pdf',
            'issued_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
