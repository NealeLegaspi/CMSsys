<?php

namespace App\Exports;

use App\Models\Grade;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Contracts\Queue\ShouldQueue;

class GradingReportExport implements FromCollection, WithHeadings, WithMapping, ShouldQueue
{
    protected $schoolYearId;

    public function __construct($schoolYearId = null)
    {
        $this->schoolYearId = $schoolYearId;
    }

    public function collection()
    {
        // Join grades → students → enrollments → subjects
        return Grade::select(
                'grades.*',
                'subjects.name as subject_name',
                'enrollments.school_year_id',
                'students.student_number'
            )
            ->join('students', 'grades.student_id', '=', 'students.id')
            ->join('enrollments', 'students.id', '=', 'enrollments.student_id')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->when($this->schoolYearId, function ($q) {
                $q->where('enrollments.school_year_id', $this->schoolYearId);
            })
            ->with(['student.user.profile'])
            ->get();
    }

    public function map($grade): array
    {
        return [
            $grade->student->student_number,
            optional($grade->student->user->profile)->first_name . ' ' . optional($grade->student->user->profile)->last_name,
            $grade->subject_name ?? '-',
            $grade->grade,
            // Fetch school year name via relationship or fallback
            optional($grade->enrollment->schoolYear ?? null)->name ?? 'N/A',
            $grade->created_at->format('Y-m-d'),
        ];
    }

    public function headings(): array
    {
        return [
            'Student Number',
            'Student Name',
            'Subject',
            'Grade',
            'School Year',
            'Recorded At',
        ];
    }
}
