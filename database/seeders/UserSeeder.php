<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        \App\Models\User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@parenthood.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Hospital One
        \App\Models\User::factory()->create([
            'name' => 'City IVF Center',
            'email' => 'hospital@cityivf.com',
            'role' => 'hospital',
            'password' => bcrypt('password'),
        ]);

        // Hospital Two
        \App\Models\User::factory()->create([
            'name' => 'Sunrise Fertility',
            'email' => 'hospital@sunrise.com',
            'role' => 'hospital',
            'password' => bcrypt('password'),
        ]);

        // Doctor
        \App\Models\User::factory()->create([
            'name' => 'Dr. A. Smith',
            'email' => 'doctor@clinic.com',
            'role' => 'doctor',
            'password' => bcrypt('password'),
        ]);
    }
}
