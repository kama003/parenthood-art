<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a Donor
        $donor = \App\Models\Donor::create([
            'donor_number' => 'D-1001',
            'age' => 25,
            'blood_group' => 'A+',
            'status' => 'active',
        ]);

        // Create a Couple
        $couple = \App\Models\Couple::create([
            'registration_number' => 'C-101',
            'partner_1_name' => 'John Doe',
            'partner_2_name' => 'Jane Doe',
            'status' => 'active',
        ]);

        // Create a Couple
        \App\Models\Couple::create([
            'registration_number' => 'C-102',
            'partner_1_name' => 'Bob Smith',
            'partner_2_name' => 'Alice Smith',
            'status' => 'active',
        ]);

        // Create Samples
        \App\Models\Sample::create([
            'sample_id' => 'S-5001',
            'donor_id' => $donor->id,
            'user_id' => 1, // Admin
            'blood_group' => 'A+',
            'vials_count' => 10,
            'freeze_date' => now(),
            'expiry_date' => now()->addYear(),
            'status' => 'available',
        ]);

        \App\Models\Sample::create([
            'sample_id' => 'S-5002',
            'donor_id' => $donor->id,
            'user_id' => 1, // Admin
            'blood_group' => 'A+',
            'vials_count' => 5,
            'freeze_date' => now(),
            'expiry_date' => now()->addYear(),
            'status' => 'available',
        ]);
    }
}
