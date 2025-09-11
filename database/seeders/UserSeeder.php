<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['email' => 'admin@example.com', 'role_id' => 1],
            ['email' => 'registrar@example.com', 'role_id' => 2],
            ['email' => 'teacher@example.com', 'role_id' => 3],
            ['email' => 'student@example.com', 'role_id' => 4],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']], // condition (unique)
                [
                    'password'   => Hash::make('password'),
                    'role_id'    => $user['role_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
