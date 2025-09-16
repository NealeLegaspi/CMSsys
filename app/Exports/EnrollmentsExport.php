<?php

namespace App\Exports;

use App\Models\Enrollment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EnrollmentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Enrollment::with(['student.user.profile','section','schoolYear'])
            ->get()
            ->map(function ($enrollment) {
                return [
                    'Student Number' => $enrollment->student->student_number ?? '',
                    'Student Name'   => optional($enrollment->student->user->profile)->first_name . ' ' .
                                       optional($enrollment->student->user->profile)->last_name,
                    'Section'        => $enrollment->section->name ?? '',
                    'School Year'    => $enrollment->schoolYear->name ?? '',
                    'Status'         => $enrollment->status,
                ];
            });
    }

    public function headings(): array
    {
        return ['Student Number', 'Student Name', 'Section', 'School Year', 'Status'];
    }
}
