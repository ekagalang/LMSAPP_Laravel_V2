<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attendance Management - {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Header -->
                    <div class="mb-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">{{ $course->title }}</h3>
                                <p class="text-gray-600 mt-1">Manage attendance for all synchronous sessions</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('courses.show', $course->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                                    Back to Course
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Content List with Attendance Requirement -->
                    @if($attendanceContents->count() > 0)
                        <!-- Overall Statistics -->
                        <div class="border-t pt-6 mt-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Overall Course Attendance</h4>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                                    <div class="text-sm opacity-90">Total Participants</div>
                                    <div class="text-3xl font-bold mt-1">{{ $course->participants->count() }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                                    <div class="text-sm opacity-90">Required Sessions</div>
                                    <div class="text-3xl font-bold mt-1">{{ $attendanceContents->count() }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
                                    <div class="text-sm opacity-90">Avg. Attendance</div>
                                    <div class="text-3xl font-bold mt-1">
                                        @php
                                            $totalRate = 0;
                                            $count = 0;
                                            foreach($attendanceContents as $content) {
                                                $stats = $content->getAttendanceStats();
                                                $totalPart = $course->participants->count();
                                                if ($totalPart > 0) {
                                                    $totalRate += ($stats['present'] / $totalPart) * 100;
                                                    $count++;
                                                }
                                            }
                                            $avgRate = $count > 0 ? round($totalRate / $count, 1) : 0;
                                        @endphp
                                        {{ $avgRate }}%
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-4 text-white">
                                    <div class="text-sm opacity-90">Completion Risk</div>
                                    <div class="text-3xl font-bold mt-1">
                                        @php
                                            // Count participants who are absent from any required session
                                            $atRisk = 0;
                                            foreach($course->participants as $participant) {
                                                foreach($attendanceContents as $content) {
                                                    $attendance = $participant->attendances()
                                                        ->where('content_id', $content->id)
                                                        ->first();
                                                    if (!$attendance || !in_array($attendance->status, ['present', 'excused'])) {
                                                        $atRisk++;
                                                        break; // Count each participant only once
                                                    }
                                                }
                                            }
                                        @endphp
                                        {{ $atRisk }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 mt-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Sessions Requiring Attendance ({{ $attendanceContents->count() }})</h4>

                            <div class="grid gap-4">
                                @foreach($attendanceContents as $content)
                                    @php
                                        $stats = $content->getAttendanceStats();
                                        $totalParticipants = $course->participants->count();
                                        $attendanceRate = $totalParticipants > 0
                                            ? round(($stats['present'] / $totalParticipants) * 100, 1)
                                            : 0;
                                    @endphp

                                    <div class="border rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3">
                                                    <h5 class="text-lg font-semibold text-gray-800">{{ $content->title }}</h5>
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Required
                                                    </span>
                                                </div>

                                                @if($content->lesson)
                                                    <p class="text-sm text-gray-600 mt-1">Lesson: {{ $content->lesson->title }}</p>
                                                @endif

                                                @if($content->scheduled_start && $content->scheduled_end)
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        <span class="font-medium">Scheduled:</span>
                                                        {{ $content->scheduled_start->format('M d, Y H:i') }} - {{ $content->scheduled_end->format('H:i') }}
                                                    </p>
                                                @endif

                                                @if($content->min_attendance_minutes)
                                                    <p class="text-sm text-blue-600 mt-1">
                                                        <span class="font-medium">Minimum Duration:</span> {{ $content->min_attendance_minutes }} minutes
                                                    </p>
                                                @endif

                                                <!-- Attendance Statistics -->
                                                <div class="flex gap-4 mt-3">
                                                    <div class="text-sm">
                                                        <span class="text-gray-600">Attendance Rate:</span>
                                                        <span class="font-semibold ml-1
                                                            @if($attendanceRate >= 80) text-green-600
                                                            @elseif($attendanceRate >= 50) text-yellow-600
                                                            @else text-red-600
                                                            @endif">
                                                            {{ $attendanceRate }}%
                                                        </span>
                                                    </div>
                                                    <div class="text-sm">
                                                        <span class="text-green-600 font-semibold">{{ $stats['present'] }}</span>
                                                        <span class="text-gray-600"> Present</span>
                                                    </div>
                                                    <div class="text-sm">
                                                        <span class="text-red-600 font-semibold">{{ $stats['absent'] }}</span>
                                                        <span class="text-gray-600"> Absent</span>
                                                    </div>
                                                    <div class="text-sm">
                                                        <span class="text-yellow-600 font-semibold">{{ $stats['late'] }}</span>
                                                        <span class="text-gray-600"> Late</span>
                                                    </div>
                                                    <div class="text-sm">
                                                        <span class="text-blue-600 font-semibold">{{ $stats['excused'] }}</span>
                                                        <span class="text-gray-600"> Excused</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex flex-col gap-2 ml-4">
                                                <a href="{{ route('attendance.index', $content->id) }}"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition text-center whitespace-nowrap">
                                                    Manage Attendance
                                                </a>
                                                <a href="{{ route('attendance.export', $content->id) }}"
                                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition text-center whitespace-nowrap">
                                                    Export CSV
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mt-3">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all
                                                    @if($attendanceRate >= 80) bg-green-500
                                                    @elseif($attendanceRate >= 50) bg-yellow-500
                                                    @else bg-red-500
                                                    @endif"
                                                    style="width: {{ $attendanceRate }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>                     

                        <!-- Participants at Risk Table -->
                        @if($atRisk > 0)
                            <div class="border-t pt-6 mt-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                                    Participants at Risk ({{ $atRisk }})
                                    <span class="text-sm font-normal text-gray-600">- Missing attendance in one or more required sessions</span>
                                </h4>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Missing Sessions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($course->participants as $participant)
                                                @php
                                                    $missingSessions = [];
                                                    foreach($attendanceContents as $content) {
                                                        $attendance = $participant->attendances()
                                                            ->where('content_id', $content->id)
                                                            ->first();
                                                        if (!$attendance || !in_array($attendance->status, ['present', 'excused'])) {
                                                            $missingSessions[] = $content->title;
                                                        }
                                                    }
                                                @endphp

                                                @if(count($missingSessions) > 0)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900">{{ $participant->name }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500">{{ $participant->email }}</div>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="flex flex-wrap gap-1">
                                                                @foreach($missingSessions as $session)
                                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                                        {{ $session }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                    @else
                        <!-- No Attendance Required -->
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Attendance Tracking Required</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                This course does not have any content that requires attendance tracking.
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                To enable attendance tracking, edit a content and set "Attendance Required" to true.
                            </p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
