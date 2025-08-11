<?php

namespace App\Services;

use App\Models\Course;
use Carbon\Carbon;

class CourseService
{
    /**
     * Create course periods based on validated data.
     */
    public function createCoursePeriods(Course $course, array $data): void
    {
        if (!empty($data['create_default_period'])) {
            $startDate = Carbon::parse($data['default_start_date']);
            $course->periods()->create([
                'name' => $course->title . ' - Periode 1',
                'start_date' => $startDate,
                'end_date' => Carbon::parse($data['default_end_date']),
                'status' => $startDate->isToday() || $startDate->isPast() ? 'active' : 'upcoming',
            ]);
        }

        if (!empty($data['periods']) && is_array($data['periods'])) {
            foreach ($data['periods'] as $periodData) {
                $startDate = Carbon::parse($periodData['start_date']);
                $course->periods()->create([
                    'name' => $periodData['name'],
                    'start_date' => $startDate,
                    'end_date' => Carbon::parse($periodData['end_date']),
                    'status' => $startDate->isToday() || $startDate->isPast() ? 'active' : 'upcoming',
                    'description' => $periodData['description'] ?? null,
                    'max_participants' => $periodData['max_participants'] ?? null,
                ]);
            }
        }
    }
}

