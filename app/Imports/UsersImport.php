<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Example CSV/Excel headings: first_name,last_name,email,role_id
        $user = User::create([
            'email'    => $row['email'],
            'password' => Hash::make('password123'), // default password
            'role_id'  => $row['role_id'],
            'status'   => 'active',
        ]);

        $user->profile()->create([
            'first_name' => $row['first_name'],
            'last_name'  => $row['last_name'],
        ]);

        return $user;
    }
}
