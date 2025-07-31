<?php

namespace Database\Seeders;

use App\Models\CertificateTemplate;
use Illuminate\Database\Seeder;

class CertificateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Template Klasik',
                'layout_data' => [
                    'styles' => [
                        'font_family' => 'Times New Roman',
                        'primary_color' => '#2c5282',
                        'secondary_color' => '#4a5568',
                        'accent_color' => '#e53e3e',
                        'background_color' => '#ffffff',
                    ],
                    'elements' => [
                        'title' => [
                            'text' => 'SERTIFIKAT PENYELESAIAN',
                            'font_size' => '48px',
                            'position' => 'center',
                        ],
                        'subtitle' => [
                            'text' => 'Certificate of Completion',
                            'font_size' => '24px',
                            'position' => 'center',
                        ],
                        'body_text' => [
                            'text' => 'Dengan ini menyatakan bahwa',
                            'font_size' => '20px',
                            'position' => 'center',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Template Modern',
                'layout_data' => [
                    'styles' => [
                        'font_family' => 'Arial',
                        'primary_color' => '#1a365d',
                        'secondary_color' => '#2d3748',
                        'accent_color' => '#3182ce',
                        'background_color' => '#f7fafc',
                    ],
                    'elements' => [
                        'title' => [
                            'text' => 'CERTIFICATE OF ACHIEVEMENT',
                            'font_size' => '42px',
                            'position' => 'center',
                        ],
                        'subtitle' => [
                            'text' => 'Sertifikat Pencapaian',
                            'font_size' => '22px',
                            'position' => 'center',
                        ],
                        'body_text' => [
                            'text' => 'This is to certify that',
                            'font_size' => '18px',
                            'position' => 'center',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Template Elegant',
                'layout_data' => [
                    'styles' => [
                        'font_family' => 'Georgia',
                        'primary_color' => '#744210',
                        'secondary_color' => '#8b5a2b',
                        'accent_color' => '#d69e2e',
                        'background_color' => '#fffbf0',
                    ],
                    'elements' => [
                        'title' => [
                            'text' => 'SERTIFIKAT KEUNGGULAN',
                            'font_size' => '46px',
                            'position' => 'center',
                        ],
                        'subtitle' => [
                            'text' => 'Certificate of Excellence',
                            'font_size' => '26px',
                            'position' => 'center',
                        ],
                        'body_text' => [
                            'text' => 'Dengan bangga menyatakan bahwa',
                            'font_size' => '20px',
                            'position' => 'center',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($templates as $template) {
            CertificateTemplate::create($template);
        }
    }
}
