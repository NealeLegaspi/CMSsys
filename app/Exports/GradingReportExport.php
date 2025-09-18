<?php

namespace App\Exports;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\SchoolYear;
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
        return Grade::with(['student.user.profile','subject','schoolYear'])
            ->when($this->schoolYearId, fn($q) => $q->where('school_year_id',$this->schoolYearId))
            ->get();
    }

    public function map($grade): array
    {
        return [
            $grade->student->student_number,
            optional($grade->student->user->profile)->first_name . ' ' . optional($grade->student->user->profile)->last_name,
            $grade->subject->name ?? '-',
            $grade->grade,
            $grade->schoolYear->name ?? '-',
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
