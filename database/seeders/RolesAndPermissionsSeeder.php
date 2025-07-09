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
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage roles']);
        Permission::create(['name' => 'manage all courses']);
        Permission::create(['name' => 'manage own courses']);
        Permission::create(['name' => 'view courses']);
        Permission::create(['name' => 'enroll courses']);
        Permission::create(['name' => 'attempt quizzes']);
        Permission::create(['name' => 'grade quizzes']);
        Permission::create(['name' => 'view progress reports']);
        Permission::create(['name' => 'generate reports']);
        Permission::create(['name' => 'manage discussions']);
        Permission::create(['name' => 'assist discussions']);


        // --- TAHAP 2: Ciptakan semua Peran ---
        $participantRole = Role::create(['name' => 'participant']);
        $instructorRole = Role::create(['name' => 'instructor']);
        $eventOrganizerRole = Role::create(['name' => 'event-organizer']);
        $superAdminRole = Role::create(['name' => 'super-admin']);


        // --- ✅ TAHAP 3: Berikan Izin Default untuk Setiap Peran ---

        // Izin untuk Participant
        $participantRole->givePermissionTo([
            'enroll courses',
            'attempt quizzes',
        ]);

        // Izin untuk Instructor
        $instructorRole->givePermissionTo([
            'view courses',
            'manage own courses', // Instruktur bisa mengelola kursus yang ditugaskan padanya
            'grade quizzes',
            'view progress reports',
            'manage discussions',
        ]);

        // Izin untuk Event Organizer
        $eventOrganizerRole->givePermissionTo([
            'view progress reports',
            'generate reports',
            'assist discussions',
        ]);

        // Super Admin secara otomatis mendapatkan semua akses melalui AuthServiceProvider,
        // jadi tidak perlu ditetapkan di sini.
    }
}