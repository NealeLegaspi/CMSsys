<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::where('role_id', 4)
            ->with(['profile','student'])
            ->get()
            ->map(function ($user) {
                return [
                    'Student Number' => $user->student->student_number ?? '',
                    'First Name'     => $user->profile->first_name ?? '',
                    'Middle Name'    => $user->profile->middle_name ?? '',
                    'Last Name'      => $user->profile->last_name ?? '',
                    'Email'          => $user->email,
                    'Gender'         => $user->profile->sex ?? '',
                    'Contact'        => $user->profile->contact_number ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return ['Student Number','First Name','Middle Name','Last Name','Email','Gender','Contact'];
    }
}
