<?php

namespace App\Exports;

use App\Models\Enrollment;
use App\Models\SchoolYear;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Contracts\Queue\ShouldQueue;

class EnrollmentReportExport implements FromCollection, WithHeadings, WithMapping, ShouldQueue
{
    protected $schoolYearId;
    protected $status;

    public function __construct($schoolYearId = null, $status = 'all')
    {
        $this->schoolYearId = $schoolYearId;
        $this->status       = $status;
    }

    public function collection()
    {
        return Enrollment::with(['student.user.profile','section.gradeLevel','schoolYear'])
            ->when($this->schoolYearId, fn($q) => $q->where('school_year_id',$this->schoolYearId))
            ->when($this->status !== 'all', fn($q) => $q->where('status',$this->status))
            ->get();
    }

    public function map($enrollment): array
    {
        return [
            $enrollment->student->student_number,
            optional($enrollment->student->user->profile)->first_name . ' ' . optional($enrollment->student->user->profile)->last_name,
            $enrollment->section->name ?? '-',
            $enrollment->section->gradeLevel->name ?? '-',
            $enrollment->schoolYear->name ?? '-',
            ucfirst($enrollment->status),
            $enrollment->created_at->format('Y-m-d'),
        ];
    }

    public function headings(): array
    {
        return [
            'Student Number',
            'Student Name',
            'Section',
            'Grade Level',
            'School Year',
            'Status',
            'Enrolled At',
        ];
    }
}
