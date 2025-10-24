<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuizTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $headers;
    protected $sampleData;

    public function __construct($headers, $sampleData)
    {
        $this->headers = $headers;
        $this->sampleData = $sampleData;
    }

    public function array(): array
    {
        return $this->sampleData;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // quiz_title
            'B' => 40, // quiz_description
            'C' => 20, // passing_percentage
            'D' => 15, // time_limit
            'E' => 30, // show_answers_after_attempt
            'F' => 25, // enable_leaderboard
            'G' => 15, // status
            'H' => 50, // question_text
            'I' => 20, // question_type
            'J' => 10, // marks
            'K' => 30, // option_1
            'L' => 30, // option_2
            'M' => 30, // option_3
            'N' => 30, // option_4
            'O' => 30, // option_5
            'P' => 30, // option_6
            'Q' => 30, // option_7
            'R' => 30, // option_8
            'S' => 30, // option_9
            'T' => 30, // option_10
            'U' => 25, // correct_answer
        ];
    }
}
