<?php

namespace App\Exports\PDF;

use App\Models\Grade;
use Barryvdh\DomPDF\Facade\Pdf;

class GradingReportPDF
{
    public static function generate($schoolYearId = null)
    {
        $grades = Grade::with(['student.user.profile','subject','schoolYear'])
            ->when($schoolYearId, fn($q) => $q->where('school_year_id',$schoolYearId))
            ->get();

        $pdf = Pdf::loadView('reports.grading-pdf', compact('grades'));
        return $pdf->download('grading_report.pdf');
    }
}
