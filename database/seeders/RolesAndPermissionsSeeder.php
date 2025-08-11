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

        // --- TAHAP 1: Ciptakan semua Izin yang ada di aplikasi ---
        $permissions = [
            'manage users',
            'manage roles', 
            'manage all courses',
            'manage own courses',
            'view courses',
            'enroll courses',
            'attempt quizzes',
            'grade quizzes',
            'view progress reports',
            'generate reports',
            'manage discussions',
            'assist discussions',
            'manage announcements',
            'manage certificates',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- TAHAP 2: Ciptakan semua Peran ---
        $participantRole = Role::firstOrCreate(['name' => 'participant']);
        $instructorRole = Role::firstOrCreate(['name' => 'instructor']);
        $eventOrganizerRole = Role::firstOrCreate(['name' => 'event-organizer']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // --- ✅ TAHAP 3: Berikan Izin Default untuk Setiap Peran ---

        // Izin untuk Participant
        $participantRole->syncPermissions([
            'enroll courses',
            'attempt quizzes',
        ]);

        // Izin untuk Instructor
        $instructorRole->syncPermissions([
            'view courses',
            'manage own courses', // Instruktur bisa mengelola kursus yang ditugaskan padanya
            'grade quizzes',
            'view progress reports',
            'manage discussions',
            'manage certificates',
        ]);

        // Izin untuk Event Organizer
        $eventOrganizerRole->syncPermissions([
            'view progress reports',
            'generate reports',
            'assist discussions',
        ]);

        // Super Admin secara otomatis mendapatkan semua akses melalui AuthServiceProvider,
        // jadi tidak perlu ditetapkan di sini.
        $superAdminRole->syncPermissions(Permission::all());
    }
}
