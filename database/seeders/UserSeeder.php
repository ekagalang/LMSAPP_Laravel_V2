<?php

namespace Database\Seeders;

use App\Models\User; // Pastikan ini ada
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Pastikan ini ada

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus user yang ada agar tidak duplikat jika seeder dijalankan berulang
        // User::truncate(); // Ini akan menghapus semua user, hati-hati di lingkungan produksi

        // Admin User
        User::firstOrCreate(
            ['email' => 'admin@lms.com'], // Kriteria untuk mencari atau membuat
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Password: password
                'role' => 'admin',
            ]
        );

        // Instructor User
        User::firstOrCreate(
            ['email' => 'instructor@lms.com'],
            [
                'name' => 'Instructor One',
                'password' => Hash::make('password'), // Password: password
                'role' => 'instructor',
            ]
        );

        // Participant User
        User::firstOrCreate(
            ['email' => 'participant@lms.com'],
            [
                'name' => 'Student Learner',
                'password' => Hash::make('password'), // Password: password
                'role' => 'participant',
            ]
        );

        // Contoh 10 peserta acak
        User::factory(10)->create();
    }
}