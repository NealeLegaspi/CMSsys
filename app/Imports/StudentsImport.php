<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|array|null
     */
    public function model(array $row)
    {
        $activeSY = SchoolYear::where('status', 'active')->first();
        if (!$activeSY) {
            return null;
        }

        $section = Section::where('name', $row['section'])
                          ->where('school_year_id', $activeSY->id)
                          ->first();

        if (!$section) {
            return null;
        }

        $baseCode = '400655';
        $lastStudent = Student::orderBy('id', 'desc')->first();
        
        $lastNumber = 0;
        if ($lastStudent && preg_match('/^' . $baseCode . '(\d{4,})$/', $lastStudent->student_number, $m)) {
            $lastNumber = (int) $m[1];
        }
        
        $studentNumber = $baseCode . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);


        $email = strtolower($row['last_name'] . '.' . $row['first_name'] . '@mindware.edu.ph');

        try {
            DB::transaction(function () use ($row, $studentNumber, $email, $section, $activeSY) {
                $cleanLast  = strtolower(preg_replace('/\s+/', '', $row['last_name']));
                $cleanFirst = strtolower(preg_replace('/\s+/', '', $row['first_name']));
                $lastFour   = substr($studentNumber, -4);

                $tempPassword = $cleanLast . $cleanFirst . $lastFour;

                // A. Create User
                $user = User::create([
                    'email' => $email,
                    'password' => Hash::make($tempPassword),
                    'role_id' => 4,
                    'status' => 'active',
                ]);

                // B. Create User Profile
                UserProfile::create([
                    'user_id' => $user->id,
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'] ?? null, // Use null for optional fields
                    'last_name' => $row['last_name'],
                    'sex' => $row['sex'],
                    'birthdate' => $row['birthdate'],
                    'address' => $row['address'],
                    'contact_number' => $row['contact_number'] ?? null,
                    'guardian_name' => $row['guardian_name'] ?? null,
                ]);

                // C. Create Student Record
                $student = Student::create([
                    'user_id' => $user->id,
                    'student_number' => $studentNumber,
                    'section_id' => $section->id,
                ]);

                // D. Create Enrollment Record
                Enrollment::create([
                    'student_id' => $student->id,
                    'section_id' => $section->id,
                    'school_year_id' => $activeSY->id,
                    'status' => 'Enrolled',
                ]);
            });
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}