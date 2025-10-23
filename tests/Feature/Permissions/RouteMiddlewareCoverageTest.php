<?php

namespace Tests\Feature\Permissions;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class RouteMiddlewareCoverageTest extends TestCase
{
    /**
     * Quickly verify that sensitive routes are protected by permission middleware.
     * This is a lightweight guard-rail to avoid manual clicking for every route.
     */
    public function test_protected_routes_have_permission_middleware(): void
    {
        $mustHavePermission = [
            // Files & uploads
            'images.upload',
            'file-control.index',
            'file-control.upload',
            'file-control.delete',
            'file-control.files',

            // Activity logs
            'activity-logs.index',
            'activity-logs.show',
            'activity-logs.clear',
            'activity-logs.export',

            // Attendance
            'attendance.export',
            'attendance.index',
            'attendance.mark',
            'attendance.bulk-mark',
            'attendance.update',
            'attendance.destroy',
            'attendance.course-report',

            // Duplication
            'courses.duplicate',
            'lessons.duplicate',
            'contents.duplicate',

            // Courses tokens
            'courses.tokens',
            'courses.token.generate',
            'courses.token.regenerate',
            'courses.token.toggle',

            // Quizzes & Essays (mgmt)
            'quizzes.index',
            'essay.questions.store',
            'essay.questions.update',
            'essay.questions.update-order',
            'essay.questions.destroy',

            // Discussions
            'courses.discussions.index',
            'discussions.store',
            'discussions.replies.store',

            // EO assign
            'courses.addEo',
            'courses.removeEo',

            // Enrollment
            'enroll',
            'enroll.course',
            'enroll.class',

            // Classes/Periods
            'course-periods.index',
            'course-periods.create',
            'course-periods.store',
            'course-periods.show',
            'course-periods.edit',
            'course-periods.update',
            'course-periods.destroy',
            'course-periods.duplicate',
            'course-periods.manage',
            'course-periods.add-instructor',
            'course-periods.remove-instructor',
            'course-periods.add-participant',
            'course-periods.remove-participant',
            'course-periods.bulk-remove-participants',
            'course-periods.enroll',
            'course-periods.token.generate',
            'course-periods.token.regenerate',
            'course-periods.token.toggle',

            // Reports
            'courses.exportProgressPdf',
            'courses.scores',

            // Certificates
            'certificates.create',
            'certificates.generate',
            'certificates.store',
            'certificates.index',
            'certificates.download',
            'courses.certificates.index',
            'courses.certificates.generate',
            'courses.certificates.bulk-generate',
            'certificates.regenerate',
            'certificates.show',
            'certificates.destroy',

            // Certificate management
            'certificate-management.index',
            'certificate-management.analytics',
            'certificate-management.by-course',
            'certificate-management.bulk-action',
            'certificate-management.update-template',

            // Analytics
            'instructor-analytics.index',
            'instructor-analytics.detail',
            'instructor-analytics.compare',

            // EO
            'eo.courses.index',
        ];

        $routes = collect(Route::getRoutes());

        foreach ($mustHavePermission as $name) {
            $route = $routes->first(fn($r) => $r->getName() === $name);
            $this->assertNotNull($route, "Route '{$name}' not found (route name mismatch?)");

            $middleware = $route->gatherMiddleware();
            $hasPermission = collect($middleware)->contains(function ($m) {
                if (!is_string($m)) return false;
                if (str_starts_with($m, 'permission:')) return true;
                // Also accept fully qualified middleware class from Spatie
                $fqcn = \Spatie\Permission\Middlewares\PermissionMiddleware::class . ':';
                return str_starts_with($m, $fqcn);
            });
            $this->assertTrue($hasPermission, "Route '{$name}' is missing permission middleware");
        }
    }
}
