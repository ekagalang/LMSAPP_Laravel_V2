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

        // Buat Permissions yang lebih spesifik
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


        // Buat Role Participant
        $participantRole = Role::create(['name' => 'participant']);
        $participantRole->givePermissionTo([
            'view courses',
            'enroll courses',
            'attempt quizzes',
        ]);

        // Buat Role Instructor
        $instructorRole = Role::create(['name' => 'instructor']);
        $instructorRole->givePermissionTo([
            'manage own courses',
            'view courses',
            'grade quizzes',
            'view progress reports',
            'manage discussions',
        ]);

        // Buat Role Event Organizer (Asisten Instruktur)
        $eventOrganizerRole = Role::create(['name' => 'event-organizer']);
        $eventOrganizerRole->givePermissionTo([
            'view courses',
            'view progress reports',
            'generate reports',
            'assist discussions',
        ]);

        // Buat Role Super Admin
        $superAdminRole = Role::create(['name' => 'super-admin']);
        // Super Admin bisa melakukan segalanya, tidak perlu assign permission satu per satu
        // karena kita akan memberikannya cek khusus di AuthServiceProvider.
    }
}