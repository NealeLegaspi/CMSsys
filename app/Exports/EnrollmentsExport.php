<?php
namespace App\Exports;

use App\Models\Enrollment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EnrollmentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Enrollment::with(['student.user.profile', 'section', 'schoolYear'])
            ->get()
            ->map(function ($e) {
                return [
                    'LRN'         => $e->student->student_number,
                    'Name'        => $e->student->user->profile->first_name . ' ' . $e->student->user->profile->last_name,
                    'Section'     => $e->section->name ?? 'N/A',
                    'School Year' => $e->schoolYear->name ?? 'N/A',
                ];
            });
    }

    public function headings(): array
    {
        return ['LRN', 'Student Name', 'Section', 'School Year'];
    }
}
