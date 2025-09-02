<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role participant sudah ada
        $participantRole = Role::firstOrCreate(['name' => 'participant']);
        
        // Data dummy participant
        $participants = [
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Dewi Sartika',
                'email' => 'dewi.sartika@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Rizky Pratama',
                'email' => 'rizky.pratama@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Maya Kusuma',
                'email' => 'maya.kusuma@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Doni Firmansyah',
                'email' => 'doni.firmansyah@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Ratna Sari',
                'email' => 'ratna.sari@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Eko Wijaya',
                'email' => 'eko.wijaya@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Linda Permata',
                'email' => 'linda.permata@example.com',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($participants as $participantData) {
            // Cek apakah user sudah ada berdasarkan email
            $user = User::where('email', $participantData['email'])->first();
            
            if (!$user) {
                // Buat user baru
                $user = User::create($participantData);
                
                // Assign role participant
                $user->assignRole('participant');
                
                echo "✅ Created participant: {$user->name} ({$user->email})\n";
            } else {
                echo "⚠️  User already exists: {$user->name} ({$user->email})\n";
            }
        }
        
        echo "\n🎉 Participant seeder completed successfully!\n";
        echo "📝 All participants have password: password123\n";
    }
}