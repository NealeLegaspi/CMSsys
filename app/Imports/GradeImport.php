<?php

namespace App\Imports;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\Section;
use App\Models\SubjectAssignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GradeImport implements ToCollection, WithHeadingRow
{
    protected $assignmentId;
    protected $teacherId;
    protected $subjectId;
    protected $sectionId;
    protected $quarterLabel; 
    protected $errors = [];

    public function __construct(int $assignmentId, int $teacherId, string $quarterLabel)
    {
        $this->assignmentId = $assignmentId;
        $this->teacherId = $teacherId;
        $this->quarterLabel = $quarterLabel;
    }

    public function collection(Collection $rows)
    {
        // Verify assignment belongs to teacher
        $assignment = SubjectAssignment::find($this->assignmentId);
        if (!$assignment || $assignment->teacher_id != $this->teacherId) {
            throw new \Exception('Unauthorized assignment or not found.');
        }

        $this->subjectId = $assignment->subject_id;
        $this->sectionId = $assignment->section_id;

        // Check locked quarter
        $locked = DB::table('grades')
            ->where('subject_id', $this->subjectId)
            ->where('quarter', $this->quarterLabel)
            ->whereIn('student_id', function ($q) {
                $q->select('id')->from('students')->where('section_id', $this->sectionId);
            })
            ->where('locked', true)
            ->exists();

        if ($locked) {
            throw new \Exception("Quarter {$this->quarterLabel} is locked for this subject/section.");
        }

        $created = $updated = $skipped = 0;

        foreach ($rows as $index => $row) {
            // Expect headings: student_number,last_name,first_name,<Qn>
            $studentNumber = trim((string)($row['student_number'] ?? ''));
            $gradeKey = null;

            // find the quarter column from headings (the 4th column)
            foreach ($row->keys() as $k) {
                if (!in_array($k, ['student_number', 'last_name', 'first_name'])) {
                    $gradeKey = $k;
                    break;
                }
            }

            $gradeValue = $gradeKey ? (string)($row[$gradeKey] ?? '') : null;
            $gradeValue = trim($gradeValue);

            // Skip empty row
            if (empty($studentNumber)) {
                $skipped++;
                continue;
            }

            // Find student by student_number
            $student = Student::where('student_number', $studentNumber)->first();
            if (!$student) {
                $this->errors[] = "Row " . ($index+2) . ": Student number {$studentNumber} not found.";
                continue;
            }

            // Check student belongs to this section & is enrolled
            $enrolled = Enrollment::where('student_id', $student->id)
                ->where('section_id', $this->sectionId)
                ->where('school_year_id', function($q){
                    $q->select('id')->from('school_years')->where('status', 'active')->limit(1);
                })
                ->where('status', 'Enrolled')
                ->exists();

            if (!$enrolled) {
                $this->errors[] = "Row " . ($index+2) . ": Student {$studentNumber} is not enrolled in this section.";
                continue;
            }

            // Validate numeric grade or empty
            if ($gradeValue === '' || $gradeValue === null) {
                // treat as deletion of existing grade for that quarter
                Grade::where('student_id', $student->id)
                    ->where('subject_id', $this->subjectId)
                    ->where('quarter', $this->quarterLabel)
                    ->delete();
                continue;
            }

            if (!is_numeric($gradeValue)) {
                $this->errors[] = "Row " . ($index+2) . ": Grade must be numeric.";
                continue;
            }

            $numeric = (float)$gradeValue;
            if ($numeric < 0 || $numeric > 100) {
                $this->errors[] = "Row " . ($index+2) . ": Grade must be between 0 and 100.";
                continue;
            }

            // Persist grade
            Grade::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $this->subjectId,
                    'quarter'    => $this->quarterLabel,
                ],
                [
                    'grade' => number_format($numeric, 2, '.', ''),
                ]
            );

            $updated++;
        }

        return [
            'errors'  => $this->errors,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }
}
