<?php

namespace Database\Factories;

use App\Models\CertificateTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateTemplateFactory extends Factory
{
    protected $model = CertificateTemplate::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true) . ' Template',
            'layout_data' => [
                'styles' => [
                    'font_family' => 'Arial',
                    'primary_color' => '#2c5282',
                    'secondary_color' => '#4a5568',
                ],
                'elements' => [
                    'title' => [
                        'text' => 'CERTIFICATE OF COMPLETION',
                        'font_size' => '48px',
                    ],
                ],
            ],
        ];
    }
}
