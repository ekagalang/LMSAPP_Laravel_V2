<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder untuk Roles dan Permissions terlebih dahulu
        $this->call(RolesAndPermissionsSeeder::class);

        // Kemudian panggil UserSeeder
        $this->call(UserSeeder::class);
    }
}
