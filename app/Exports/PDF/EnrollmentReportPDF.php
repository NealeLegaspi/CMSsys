<?php

namespace App\Exports\PDF;

use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;

class EnrollmentReportPDF
{
    public static function generate($schoolYearId = null, $status = 'all')
    {
        $enrollments = Enrollment::with(['student.user.profile','section.gradeLevel','schoolYear'])
            ->when($schoolYearId, fn($q) => $q->where('school_year_id',$schoolYearId))
            ->when($status !== 'all', fn($q) => $q->where('status',$status))
            ->get();

        $pdf = Pdf::loadView('reports.enrollment-pdf', compact('enrollments'));
        return $pdf->download('enrollment_report.pdf');
    }
}
