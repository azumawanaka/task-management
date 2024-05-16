<?php

namespace Database\Seeders;

use App\Enums\RoleType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::truncate();
        User::factory()->create([
            'name' => 'John Doe',
            'user_name' => 'john_doe',
            'email' => 'john_doe@example.com',
            'password' => Hash::make('password'),
        ]);
        User::factory()->create([
            'name' => 'Administrator',
            'user_name' => 'administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => RoleType::ADMIN,
        ]);
    }
}
