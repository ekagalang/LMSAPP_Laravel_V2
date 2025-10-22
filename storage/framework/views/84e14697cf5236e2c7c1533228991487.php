<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attendance Management - <?php echo e($course->title); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Header -->
                    <div class="mb-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800"><?php echo e($course->title); ?></h3>
                                <p class="text-gray-600 mt-1">Manage attendance for all synchronous sessions</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="<?php echo e(route('courses.show', $course->id)); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                                    Back to Course
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Content List with Attendance Requirement -->
                    <?php if($attendanceContents->count() > 0): ?>
                        <!-- Overall Statistics -->
                        <div class="border-t pt-6 mt-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Overall Course Attendance</h4>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                                    <div class="text-sm opacity-90">Total Participants</div>
                                    <div class="text-3xl font-bold mt-1"><?php echo e($course->participants->count()); ?></div>
                                </div>
                                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                                    <div class="text-sm opacity-90">Required Sessions</div>
                                    <div class="text-3xl font-bold mt-1"><?php echo e($attendanceContents->count()); ?></div>
                                </div>
                                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
                                    <div class="text-sm opacity-90">Avg. Attendance</div>
                                    <div class="text-3xl font-bold mt-1">
                                        <?php
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
                                        ?>
                                        <?php echo e($avgRate); ?>%
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-4 text-white">
                                    <div class="text-sm opacity-90">Completion Risk</div>
                                    <div class="text-3xl font-bold mt-1">
                                        <?php
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
                                        ?>
                                        <?php echo e($atRisk); ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 mt-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Sessions Requiring Attendance (<?php echo e($attendanceContents->count()); ?>)</h4>

                            <div class="grid gap-4">
                                <?php $__currentLoopData = $attendanceContents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $stats = $content->getAttendanceStats();
                                        $totalParticipants = $course->participants->count();
                                        $attendanceRate = $totalParticipants > 0
                                            ? round(($stats['present'] / $totalParticipants) * 100, 1)
                                            : 0;
                                    ?>

                                    <div class="border rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3">
                                                    <h5 class="text-lg font-semibold text-gray-800"><?php echo e($content->title); ?></h5>
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Required
                                                    </span>
                                                </div>

                                                <?php if($content->lesson): ?>
                                                    <p class="text-sm text-gray-600 mt-1">Lesson: <?php echo e($content->lesson->title); ?></p>
                                                <?php endif; ?>

                                                <?php if($content->scheduled_start && $content->scheduled_end): ?>
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        <span class="font-medium">Scheduled:</span>
                                                        <?php echo e($content->scheduled_start->format('M d, Y H:i')); ?> - <?php echo e($content->scheduled_end->format('H:i')); ?>

                                                    </p>
                                                <?php endif; ?>

                                                <?php if($content->min_attendance_minutes): ?>
                                                    <p class="text-sm text-blue-600 mt-1">
                                                        <span class="font-medium">Minimum Duration:</span> <?php echo e($content->min_attendance_minutes); ?> minutes
                                                    </p>
                                                <?php endif; ?>

                                                <!-- Attendance Statistics -->
                                                <div class="flex gap-4 mt-3">
                                                    <div class="text-sm">
                                                        <span class="text-gray-600">Attendance Rate:</span>
                                                        <span class="font-semibold ml-1
                                                            <?php if($attendanceRate >= 80): ?> text-green-600
                                                            <?php elseif($attendanceRate >= 50): ?> text-yellow-600
                                                            <?php else: ?> text-red-600
                                                            <?php endif; ?>">
                                                            <?php echo e($attendanceRate); ?>%
                                                        </span>
                                                    </div>
                                                    <div class="text-sm">
                                                        <span class="text-green-600 font-semibold"><?php echo e($stats['present']); ?></span>
                                                        <span class="text-gray-600"> Present</span>
                                                    </div>
                                                    <div class="text-sm">
                                                        <span class="text-red-600 font-semibold"><?php echo e($stats['absent']); ?></span>
                                                        <span class="text-gray-600"> Absent</span>
                                                    </div>
                                                    <div class="text-sm">
                                                        <span class="text-yellow-600 font-semibold"><?php echo e($stats['late']); ?></span>
                                                        <span class="text-gray-600"> Late</span>
                                                    </div>
                                                    <div class="text-sm">
                                                        <span class="text-blue-600 font-semibold"><?php echo e($stats['excused']); ?></span>
                                                        <span class="text-gray-600"> Excused</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex flex-col gap-2 ml-4">
                                                <a href="<?php echo e(route('attendance.index', $content->id)); ?>"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition text-center whitespace-nowrap">
                                                    Manage Attendance
                                                </a>
                                                <a href="<?php echo e(route('attendance.export', $content->id)); ?>"
                                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition text-center whitespace-nowrap">
                                                    Export CSV
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mt-3">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all
                                                    <?php if($attendanceRate >= 80): ?> bg-green-500
                                                    <?php elseif($attendanceRate >= 50): ?> bg-yellow-500
                                                    <?php else: ?> bg-red-500
                                                    <?php endif; ?>"
                                                    style="width: <?php echo e($attendanceRate); ?>%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>                     

                        <!-- Participants at Risk Table -->
                        <?php if($atRisk > 0): ?>
                            <div class="border-t pt-6 mt-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                                    Participants at Risk (<?php echo e($atRisk); ?>)
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
                                            <?php $__currentLoopData = $course->participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $participant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $missingSessions = [];
                                                    foreach($attendanceContents as $content) {
                                                        $attendance = $participant->attendances()
                                                            ->where('content_id', $content->id)
                                                            ->first();
                                                        if (!$attendance || !in_array($attendance->status, ['present', 'excused'])) {
                                                            $missingSessions[] = $content->title;
                                                        }
                                                    }
                                                ?>

                                                <?php if(count($missingSessions) > 0): ?>
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900"><?php echo e($participant->name); ?></div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500"><?php echo e($participant->email); ?></div>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="flex flex-wrap gap-1">
                                                                <?php $__currentLoopData = $missingSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                                        <?php echo e($session); ?>

                                                                    </span>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
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
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/attendance/course-report.blade.php ENDPATH**/ ?>