<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- TAHAP 1: Definisikan seluruh permission yang diperlukan (idempotent) ---
        $permissions = [
            // Users & Roles
            'manage users',
            'manage roles',

            // Courses (global dan milik sendiri)
            'view courses',
            'enroll courses',
            'manage all courses',
            'manage own courses',
            'create courses',
            'update courses',
            'delete courses',
            'publish courses',
            'duplicate courses',
            'assign instructors',
            'assign event organizers',
            'manage course tokens',

            // Classes / Periods
            'view classes',
            'create classes',
            'update classes',
            'delete classes',
            'duplicate classes',
            'enroll class participants',
            'manage class tokens',
            'assign class instructors',
            'remove class instructors',
            'add class participants',
            'remove class participants',

            // Lessons
            'create lessons',
            'update lessons',
            'delete lessons',
            'duplicate lessons',

            // Contents
            'create contents',
            'update contents',
            'delete contents',
            'duplicate contents',
            'schedule zoom',

            // Attendance
            'view attendance',
            'mark attendance',
            'bulk mark attendance',
            'update attendance',
            'delete attendance',
            'export attendance',
            'view attendance reports',

            // Quizzes & Essays
            'view quizzes',
            'create quizzes',
            'update quizzes',
            'delete quizzes',
            'attempt quizzes',
            'grade quizzes',
            'manage essay questions',
            'view essay submissions',
            'grade essays',

            // Discussions
            'view discussions',
            'create discussions',
            'reply discussions',
            'manage discussions', // moderasi
            'assist discussions',

            // Chats
            'create course chats',
            'create chats',
            'send chat messages',
            'add chat participants',
            'remove chat participants',

            // Certificates
            'view certificates',
            'issue certificates',
            'bulk issue certificates',
            'regenerate certificates',
            'delete certificates',
            'download certificates',
            'view certificate management',
            'view certificate analytics',
            'update certificate template',

            // Certificate Templates (Admin)
            'view certificate templates',
            'create certificate templates',
            'update certificate templates',
            'delete certificate templates',
            'duplicate certificate templates',
            'preview certificate templates',

            // Reports / Analytics
            'view progress reports',
            'generate reports',
            'view instructor analytics',

            // Announcements
            'view announcements',
            'create announcements',
            'update announcements',
            'delete announcements',
            'publish announcements',

            // Activity Logs
            'view activity logs',
            'export activity logs',
            'clear activity logs',

            // File Control / Uploads
            'view files',
            'upload files',
            'delete files',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }


        // --- TAHAP 2: Ciptakan semua Peran ---
        $participantRole = Role::firstOrCreate(['name' => 'participant']);
        $instructorRole = Role::firstOrCreate(['name' => 'instructor']);
        $eventOrganizerRole = Role::firstOrCreate(['name' => 'event-organizer']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);


        // --- ✅ TAHAP 3: Berikan Izin Default untuk Setiap Peran ---
        // Jangan overwrite di production kecuali diizinkan
        $overwrite = (bool) env('SEED_ROLES_OVERWRITE', false);

        $participantDefaults = [
            'view courses', 'view announcements', 'enroll courses', 'attempt quizzes', 'view quizzes',
            'create discussions', 'reply discussions', 'view certificates', 'download certificates',
            // Chats
            'create chats', 'send chat messages',
        ];

        $instructorDefaults = [
            'view courses', 'manage own courses',
            // File Control
            'view files', 'upload files', 'delete files',
            // Classes / Periods
            'view classes', 'create classes', 'update classes', 'duplicate classes',
            'assign class instructors', 'remove class instructors',
            'add class participants', 'remove class participants', 'enroll class participants',
            'manage class tokens',
            // Lessons & Contents
            'create lessons', 'update lessons', 'delete lessons',
            'create contents', 'update contents', 'delete contents', 'schedule zoom',
            // Course tokens (course-level)
            'manage course tokens',
            // Quizzes & Essays
            'view quizzes', 'create quizzes', 'update quizzes', 'delete quizzes', 'grade quizzes',
            'manage essay questions', 'view essay submissions', 'grade essays',
            // Discussions & Attendance
            'manage discussions', 'view attendance', 'mark attendance', 'bulk mark attendance', 'update attendance', 'export attendance', 'view attendance reports',
            // Certificates (course-level)
            'view certificates', 'issue certificates', 'bulk issue certificates', 'regenerate certificates', 'download certificates',
            // Reports / Analytics
            'view progress reports', 'view instructor analytics',
            // Chats
            'create chats', 'send chat messages', 'create course chats', 'add chat participants', 'remove chat participants',
        ];

        $eventOrganizerDefaults = [
            'view courses', 'view classes', 'view files',
            'view progress reports', 'view instructor analytics', 'generate reports', 'assist discussions',
            'view attendance reports', 'view certificate management', 'view certificate analytics',
            // Chats
            'create chats', 'send chat messages', 'create course chats', 'add chat participants', 'remove chat participants',
        ];

        $applyDefaults = function (Role $role, array $defaults) use ($overwrite) {
            if ($overwrite) {
                $role->syncPermissions($defaults);
                return;
            }
            if ($role->permissions()->count() === 0) {
                $role->givePermissionTo($defaults);
            }
        };

        $applyDefaults($participantRole, $participantDefaults);
        $applyDefaults($instructorRole, $instructorDefaults);
        $applyDefaults($eventOrganizerRole, $eventOrganizerDefaults);

        // Super Admin: full akses via Gate::before
    }
}
