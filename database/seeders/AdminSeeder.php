<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Delete all users except the one with ID 1
        DB::table('users')->where('id', '!=', 1)->delete();

        // Reset AUTO_INCREMENT so the next user will start from 2
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 2;');

        // Create or update the admin user with ID = 1
        User::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'), // Change this to a secure password
                'role' => 'admin', // Adjust this if your users table uses a different column or enum
            ]
        );

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
