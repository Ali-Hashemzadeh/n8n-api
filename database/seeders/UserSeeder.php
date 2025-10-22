<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default admin user
        User::create([
            'name' => 'Ali Melmedas',
            'email' => 'ali.melmedas1383@gmail.com',
            'mobile' => '09197238119',
            'password' => Hash::make('password')
        ]);

        // You could add more users here if you wanted
        // User::create([ ... ]);
    }
}
