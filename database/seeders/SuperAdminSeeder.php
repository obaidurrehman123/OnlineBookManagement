<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => env('SUPERADMIN_USERNAME'),
            'email' => env('SUPERADMIN_EMAIL'),
            'password' => bcrypt(env('SUPERADMIN_PASSWORD')),
            'role' => 'admin',
        ]);
    }
}
