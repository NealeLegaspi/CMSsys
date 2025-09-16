<?php

namespace App\Imports;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Section;
use App\Models\SchoolYear;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EnrollmentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $student = Student::where('student_number', $row['student_number'])->first();
        $section = Section::where('name', $row['section'])->first();
        $schoolYear = SchoolYear::where('name', $row['school_year'])->first();

        if ($student && $section && $schoolYear) {
            return new Enrollment([
                'student_id'     => $student->id,
                'section_id'     => $section->id,
                'school_year_id' => $schoolYear->id,
                'status'         => $row['status'] ?? 'enrolled',
            ]);
        }

        return null;
    }
}
