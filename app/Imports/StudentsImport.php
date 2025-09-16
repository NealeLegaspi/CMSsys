<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = User::create([
            'email'    => $row['email'],
            'password' => Hash::make('password123'),
            'role_id'  => 4,
        ]);

        $user->profile()->create([
            'first_name'     => $row['first_name'],
            'middle_name'    => $row['middle_name'] ?? null,
            'last_name'      => $row['last_name'],
            'sex'            => $row['gender'] ?? null,
            'contact_number' => $row['contact'] ?? null,
        ]);

        $user->student()->create([
            'student_number' => $row['student_number'],
        ]);

        return $user;
    }
}
