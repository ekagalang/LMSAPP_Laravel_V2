<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        // Reset cached roles and permissions for each test
        if (class_exists(PermissionRegistrar::class)) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    /**
     * Seed roles and permissions for testing
     */
    protected function seedRolesAndPermissions(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    /**
     * Create a user with specific role
     */
    protected function createUserWithRole(string $role): \App\Models\User
    {
        $this->seedRolesAndPermissions();
        
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);
        
        return $user;
    }

    /**
     * Create admin user for testing
     */
    protected function createAdminUser(): \App\Models\User
    {
        return $this->createUserWithRole('super-admin');
    }

    /**
     * Create instructor user for testing
     */
    protected function createInstructorUser(): \App\Models\User
    {
        return $this->createUserWithRole('instructor');
    }

    /**
     * Create participant user for testing
     */
    protected function createParticipantUser(): \App\Models\User
    {
        return $this->createUserWithRole('participant');
    }

    /**
     * Create event organizer user for testing
     */
    protected function createEventOrganizerUser(): \App\Models\User
    {
        return $this->createUserWithRole('event-organizer');
    }
}