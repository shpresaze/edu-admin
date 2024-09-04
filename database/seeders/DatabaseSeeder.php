<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'phone_number' => '072687746',
            'embg' => '1234567890123'
        ]);

        \App\Models\User::factory()->create([
            'name' => 'teacher',
            'email' => 'teacher@gmail.com',
            'password' => Hash::make('teacher'),
            'role' => 'teacher',
            'phone_number' => '071687789',
            'embg' => '1234567890121'
        ]);
    }
}
