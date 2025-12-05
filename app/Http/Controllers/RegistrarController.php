<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\GradeLevel;
use App\Models\Grade;
use App\Models\UserProfile;
use App\Models\ActivityLog;
use App\Models\StudentDocument; 
use App\Models\StudentCertificate; 
use App\Models\SubjectAssignment;
use App\Models\Curriculum;
use App\Helpers\SystemHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Exports\EnrollmentsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\StudentTemplateExport;

class RegistrarController extends Controller
{
    /**
     * Log Activity
     */
    protected function logActivity($action, $description)
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'description'=> $description,
        ]);
    }

    /**
     * Dashboard Overview
     */
    public function dashboard()
    {
        $activeSY = SchoolYear::where('status', 'active')->first();

        if (!$activeSY) {
            return view('registrars.dashboard', [
                'noActiveSY' => true,
                'sections' => collect(),
                'genderLabels' => [],
                'genderData' => [],
            ]);
        }

        $studentCount = User::where('role_id', 4)->count();
        $teacherCount = User::where('role_id', 3)->count();
        $sectionCount = Section::count();

        // Students per Section
        $sections = Section::pluck('name');
        $totals   = $sections->map(fn($sec) =>
            Enrollment::whereHas('section', fn($q) => $q->where('name', $sec))->count()
        );

        // Gender Distribution
        $genderLabels = ['Male', 'Female'];
        $genderData   = [
            UserProfile::where('sex', 'Male')->count(),
            UserProfile::where('sex', 'Female')->count(),
        ];

        return view('registrars.dashboard', compact(
            'studentCount', 'teacherCount', 'sectionCount',
            'sections', 'totals', 'genderLabels', 'genderData', 'activeSY'
        ));
    }


    /**
     * Student Records
     */
    public function students(Request $request)
    {
        $query = User::where('role_id', 4)
            ->whereHas('student.enrollments', function ($q) {
                $q->where('status', 'enrolled');
            })
            ->with(['profile', 'student.section']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                ->orWhereHas('profile', function ($sub) use ($search) {
                    $sub->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->orWhereHas('student', function ($sub) use ($search) {
                    $sub->where('student_number', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('section_id')) {
            $query->whereHas('student.section', fn($q) => $q->where('id', $request->section_id));
        }

        $students = $query->latest()->paginate(10)->withQueryString();
        $sections = Section::all();

        return view('registrars.students', compact('students', 'sections'));
    }


    public function showStudent($id)
    {
        $student = User::with(['profile', 'student.section.gradeLevel'])
                    ->where('role_id', 4)
                    ->findOrFail($id);

        return view('registrars.students.show', compact('student'));
    }


    public function storeStudent(Request $request)
    {
        $request->validate([
            'email'       => 'required|email|unique:users,email',
            'first_name'  => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name'   => 'required|string|max:50',
        ]);

        DB::transaction(function () use ($request, &$studentNumber) {
            $user = User::create([
                'email'    => $request->email,
                'password' => bcrypt('password123'),
                'role_id'  => 4,
                'status'   => 'pending',
            ]);

            $user->profile()->create([
                'first_name'  => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name'   => $request->last_name,
            ]);

            do {
                $year   = now()->format('Y');
                $random = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $studentNumber = $year . $random;
            } while (Student::where('student_number', $studentNumber)->exists());

            $user->student()->create([
                'student_number' => $studentNumber,
            ]);
        });

        $this->logActivity('Add Student',"Added new student {$request->email} with Student No: {$studentNumber}");

        return back()->with('success', 'Student added successfully. Student No: ' . $studentNumber);
    }

    public function updateStudent(Request $request, $id)
    {
        $student = User::with('profile')->findOrFail($id);

        $request->validate([
            'first_name'  => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name'   => 'required|string|max:50',
            'email'       => ['required', 'email', Rule::unique('users', 'email')->ignore($student->id)],
        ]);

        $student->update(['email' => $request->email]);

        $student->profile?->update([
            'first_name'  => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'   => $request->last_name,
        ]);

        $this->logActivity('Update Student',"Updated student {$student->email}");

        return back()->with('success', 'Student updated successfully.');
    }

    public function destroyStudent($id)
    {
        $student = User::findOrFail($id);

        if ($student->role_id !== 4) {
            return back()->withErrors(['error' => 'Not a student account.']);
        }

        $student->delete();

        $this->logActivity('Delete Student',"Deleted student {$student->email}");

        return back()->with('success', 'Student deleted.');
    }

    public function exportStudents()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function importStudents(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv,txt'
        ]);


        try {
            Excel::import(new StudentsImport, $request->file('import_file'));
            $this->logActivity('Import Students', "Imported student list via Excel.");

            return back()->with('success', 'Students successfully imported and enrolled!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new StudentTemplateExport, 'student_import_template.xlsx');
    }


    public function viewStudentRecord($id)
    {
        $student = Student::with([
            'user.profile',
            'enrollments.section.gradeLevel',
            'enrollments.schoolYear',
        ])->findOrFail($id);

        $currentSY = $this->getCurrentSY();

        $documents = StudentDocument::where('student_id', $id)
            ->when($currentSY, fn($q) => $q->where('school_year_id', $currentSY->id))
            ->latest()
            ->get();

        $certificates = StudentCertificate::where('student_id', $id)
            ->when($currentSY, fn($q) => $q->where('school_year_id', $currentSY->id))
            ->latest()
            ->get();

        $grades = Grade::with(['subject', 'enrollment.section.gradeLevel'])
            ->where('student_id', $id)
            ->get();

        return view('registrars.student-record', compact('student', 'documents', 'certificates', 'grades', 'currentSY'));
    }

    public function exportStudentRecordPDF($id)
    {
        $student = Student::with([
            'user.profile',
            'enrollments.section.gradeLevel',
            'enrollments.schoolYear',
        ])->findOrFail($id);

        $currentSY = $this->getCurrentSY();

        $documents = StudentDocument::where('student_id', $id)
            ->when($currentSY, fn($q) => $q->where('school_year_id', $currentSY->id))
            ->latest()
            ->get();

        $certificates = StudentCertificate::where('student_id', $id)
            ->when($currentSY, fn($q) => $q->where('school_year_id', $currentSY->id))
            ->latest()
            ->get();

        $grades = Grade::with(['subject', 'enrollment.section.gradeLevel'])
            ->where('student_id', $id)
            ->get();

        $pdf = Pdf::loadView('exports.student-record-pdf', compact('student', 'documents', 'certificates', 'grades'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Student_Record_' . ($student->user->profile->last_name ?? 'Student') . '.pdf');
    }

public function printForm137($studentId)
{
    return view('registrars.forms.form137', compact('student', 'grades'));
}

public function printForm138($studentId)
{
    return view('registrars.forms.form138', compact('student', 'grades'));
}

    /**
     * Enrollment
     */
    public function enrollment(Request $request)
    {
        // Active school year
        $activeSY = SchoolYear::where('status', 'active')->first();
        $activeSYId = $activeSY ? $activeSY->id : null;

        // Students NOT yet enrolled in active SY
        $enrolledStudentIds = $activeSYId
            ? Enrollment::where('school_year_id', $activeSYId)->pluck('student_id')
            : collect([]);

        $studentsNotEnrolled = Student::with('user.profile')
            ->whereNotIn('id', $enrolledStudentIds)
            ->get();

        // All school years & sections for selects
        $schoolYears = SchoolYear::all();
        $sections = Section::with('gradeLevel', 'enrollments')
            ->where('school_year_id', $activeSYId)
            ->get();

        /**
         * Promotion safety logic for Student Tools
         * ---------------------------------------
         * For each student who is not yet enrolled in the active SY:
         *  - Find their last enrollment (latest school year).
         *  - Compute the year gap between last enrollment SY and active SY.
         *  - Allow sections only in:
         *      gap = 1 → same grade + next grade
         *      gap >=2 → same grade + next 2 grades (if they exist)
         *  - If no previous enrollment or no active SY: allow all sections.
         */
        $studentUpgradeInfo = [];

        if ($activeSY && $studentsNotEnrolled->isNotEmpty()) {
            // Map school years into an ordered index so we can compute the gap.
            $orderedSchoolYears = SchoolYear::orderBy('start_date')->get();
            $syIndex = [];
            foreach ($orderedSchoolYears as $index => $sy) {
                $syIndex[$sy->id] = $index;
            }

            // Ordered grade levels for progression.
            $orderedGrades = GradeLevel::orderBy('id')->get();
            $gradeIndex = [];
            foreach ($orderedGrades as $index => $gl) {
                $gradeIndex[$gl->id] = $index;
            }

            $studentIds = $studentsNotEnrolled->pluck('id');

            $lastEnrollments = Enrollment::with(['section.gradeLevel', 'schoolYear'])
                ->whereIn('student_id', $studentIds)
                ->where('archived', false)
                ->get()
                ->sortByDesc(function ($enr) use ($syIndex) {
                    return $syIndex[$enr->school_year_id] ?? -1;
                })
                ->groupBy('student_id')
                ->map->first();

            foreach ($studentsNotEnrolled as $student) {
                $info = [
                    'last_sy_name'         => null,
                    'last_grade_name'      => null,
                    'allowed_grade_names'  => [],
                    'allowed_section_ids'  => $sections->pluck('id')->all(), // default: all sections
                ];

                $last = $lastEnrollments->get($student->id);

                if ($last && isset($syIndex[$last->school_year_id]) && isset($syIndex[$activeSYId])) {
                    $lastIndex   = $syIndex[$last->school_year_id];
                    $activeIndex = $syIndex[$activeSYId];
                    $gap         = max(0, $activeIndex - $lastIndex); // how many SYs have passed

                    $info['last_sy_name']    = optional($last->schoolYear)->name;
                    $info['last_grade_name'] = optional(optional($last->section)->gradeLevel)->name;

                    $lastGradeId = optional($last->section)->gradelevel_id;

                    if ($gap > 0 && $lastGradeId && isset($gradeIndex[$lastGradeId])) {
                        $maxAdvance = min($gap, 2); // at most 2 grade levels ahead
                        $startIdx  = $gradeIndex[$lastGradeId];
                        $endIdx    = min($startIdx + $maxAdvance, $orderedGrades->count() - 1);

                        $allowedGradeIds   = [];
                        $allowedGradeNames = [];

                        for ($i = $startIdx; $i <= $endIdx; $i++) {
                            $allowedGradeIds[]   = $orderedGrades[$i]->id;
                            $allowedGradeNames[] = $orderedGrades[$i]->name;
                        }

                        $info['allowed_grade_names'] = $allowedGradeNames;

                        // Restrict sections to only those grade levels in the active SY
                        $info['allowed_section_ids'] = $sections
                            ->whereIn('gradelevel_id', $allowedGradeIds)
                            ->pluck('id')
                            ->all();
                    }
                }

                $studentUpgradeInfo[$student->id] = $info;
            }
        }

        // --- Active SY Enrollments (for the Student Registration tab) ---
        $activeQuery = Enrollment::with(['student.user.profile', 'section.gradeLevel', 'section.adviser.profile', 'schoolYear'])
            ->when($activeSYId, fn($q) => $q->where('school_year_id', $activeSYId))
            ->where('archived', false);

        if ($request->filled('search_active')) {
            $search = $request->search_active;
            $activeQuery->whereHas('student.user.profile', fn($q) => 
                $q->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
            );
        }

        $activeEnrollments = $activeQuery->latest()->paginate(10, ['*'], 'active_page');

        // --- Enrollment History (all non-archived enrollments, paginated separately) ---
        $historyQuery = Enrollment::with(['student.user.profile', 'section.gradeLevel', 'schoolYear'])
            ->where('archived', false);

        if ($request->filled('search_history')) {
            $search = $request->search_history;
            $historyQuery->where(function($q) use ($search) {
                $q->whereHas('student.user.profile', fn($q2) => 
                    $q2->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                )->orWhereHas('section', fn($q2) => 
                    $q2->where('name', 'like', "%$search%")
                )->orWhereHas('schoolYear', fn($q2) =>
                    $q2->where('name', 'like', "%$search%")
                );
            });
        }

        if ($request->filled('history_school_year_id')) {
            $historyQuery->where('school_year_id', $request->history_school_year_id);
        }

        $historyEnrollments = $historyQuery->latest()->paginate(10, ['*'], 'history_page');

        // --- Archived enrollments (separate) ---
        $archivedQuery = Enrollment::with(['student.user.profile', 'section.gradeLevel', 'schoolYear'])
            ->where('archived', true);

        if ($request->filled('search_archived')) {
            $search = $request->search_archived;
            $archivedQuery->whereHas('student.user.profile', fn($q) =>
                $q->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
            );
        }

        $archivedEnrollments = $archivedQuery->latest()->paginate(10, ['*'], 'archived_page');

        // Get teachers for adviser dropdown
        $teachers = User::where('role_id', 3)
            ->where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'inactive');
            })
            ->with('profile')
            ->get();

        // Get all subjects (from current curriculum) by grade level and their assignments for each section (for displaying in modal)
        $sectionSubjectAssignments = [];
        if ($activeSYId) {
            // Get all sections with their grade levels
            $sectionsWithGrades = Section::where('school_year_id', $activeSYId)
                ->with('gradeLevel')
                ->get();

            // Determine the current curriculum for this school year
            $currentCurriculum = Curriculum::where('school_year_id', $activeSYId)
                ->where('is_template', false)
                ->orderByDesc('id')
                ->first();

            // Get all subjects from the current curriculum, grouped by grade level
            $allSubjects = collect();
            if ($currentCurriculum) {
                $allSubjects = $currentCurriculum->subjects()
                    ->where('is_archived', false)
                    ->with('gradeLevel')
                    ->get()
                    ->groupBy('grade_level_id');
            }

            // Get all subject assignments (section_id, subject_id, teacher info)
            $allAssignments = DB::table('subject_teacher')
                ->join('sections', 'subject_teacher.section_id', '=', 'sections.id')
                ->join('subjects', 'subject_teacher.subject_id', '=', 'subjects.id')
                ->join('users', 'subject_teacher.teacher_id', '=', 'users.id')
                ->join('profiles', 'users.id', '=', 'profiles.user_id')
                ->where('sections.school_year_id', $activeSYId)
                ->select(
                    'subject_teacher.section_id',
                    'subject_teacher.subject_id',
                    'subjects.name as subject_name',
                    'profiles.first_name',
                    'profiles.last_name'
                )
                ->get()
                ->groupBy('section_id')
                ->map(function($group) {
                    return $group->keyBy('subject_id');
                });

            // For each section, get all subjects for its grade level
            foreach ($sectionsWithGrades as $section) {
                $gradeLevelId = $section->gradelevel_id;
                $sectionId = (string)$section->id;
                
                // Get all subjects for this grade level
                $gradeSubjects = $allSubjects->get($gradeLevelId, collect());
                
                // Build the subject list with assignment info
                $sectionSubjectAssignments[$sectionId] = $gradeSubjects->map(function($subject) use ($allAssignments, $sectionId) {
                    $sectionAssignments = $allAssignments->get($sectionId);

                    // Prefer exact subject_id match; if not found, fall back to same-name match within this section
                    $assignment = $sectionAssignments?->get($subject->id);
                    if (!$assignment && $sectionAssignments) {
                        $assignment = $sectionAssignments->firstWhere('subject_name', $subject->name);
                    }

                    return [
                        'subject_id' => $subject->id,
                        'subject_name' => $subject->name,
                        'teacher_name' => $assignment 
                            ? trim(($assignment->first_name ?? '') . ' ' . ($assignment->last_name ?? ''))
                            : 'Not Assigned'
                    ];
                })->values()->toArray();
            }
        }

        return view('registrars.enrollment', compact(
            'studentsNotEnrolled',
            'sections',
            'schoolYears',
            'activeSY',
            'activeEnrollments',
            'historyEnrollments',
            'archivedEnrollments',
            'studentUpgradeInfo',
            'teachers',
            'sectionSubjectAssignments'
        ));
    }

    /**
     * Find an available section for a given grade level that has capacity.
     * Returns the section ID or null if all sections are full.
     */
    private function findAvailableSection($gradeLevelId, $schoolYearId, $excludeSectionId = null)
    {
        $sections = Section::where('gradelevel_id', $gradeLevelId)
            ->where('school_year_id', $schoolYearId)
            ->when($excludeSectionId, function ($q) use ($excludeSectionId) {
                $q->where('id', '!=', $excludeSectionId);
            })
            ->get();

        foreach ($sections as $section) {
            if (!$section->capacity) {
                // No capacity limit, so it's available
                return $section;
            }

            $enrolledCount = Enrollment::where('section_id', $section->id)
                ->where('school_year_id', $schoolYearId)
                ->count();

            if ($enrolledCount < $section->capacity) {
                return $section;
            }
        }

        return null; // All sections are full
    }

    public function storeEnrollment(Request $request)
    {
        $activeSY = SchoolYear::where('status', 'active')->first();
        if (!$activeSY) {
            return back()->withErrors(['school_year' => 'No active school year. Actions are disabled.']);
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'sex' => 'required|string',
            'birthdate' => 'required|date',
            'contact_number' => 'required|string|max:20',
            'guardian_first_name' => 'required|string|max:255',
            'guardian_middle_name' => 'nullable|string|max:255',
            'guardian_last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'adviser_id' => 'nullable|exists:users,id',
        ]);

        $exists = Enrollment::where('student_id', $request->student_id)
            ->where('school_year_id', $activeSY->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['student_id' => 'This student is already enrolled in the active school year.']);
        }

        $section = Section::findOrFail($request->section_id);
        $gradeLevelId = $section->gradelevel_id;
        $gradeLevelName = $section->gradeLevel->name ?? 'this grade level';

        // Check if selected section has capacity
        $selectedSectionFull = false;
        if ($section->capacity) {
            $count = Enrollment::where('section_id', $section->id)
                ->where('school_year_id', $activeSY->id)
                ->count();
            if ($count >= $section->capacity) {
                $selectedSectionFull = true;
            }
        }

        // If selected section is full, try to find another section with the same grade level
        if ($selectedSectionFull) {
            $availableSection = $this->findAvailableSection($gradeLevelId, $activeSY->id, $section->id);
            
            if (!$availableSection) {
                // All sections of this grade level are full
                return back()->withErrors([
                    'section_id' => "All sections for {$gradeLevelName} are full. Cannot enroll more students in {$gradeLevelName}."
                ]);
            }

            // Use the available section instead
            $section = $availableSection;
            $request->merge(['section_id' => $section->id]);
        }

        $student = Student::with('user.profile')->findOrFail($request->student_id);

        // Update student profile if provided
        if ($student->user && $student->user->profile) {
            // Concatenate guardian name fields
            $guardianName = trim($request->guardian_first_name . ' ' . 
                ($request->guardian_middle_name ? $request->guardian_middle_name . ' ' : '') . 
                $request->guardian_last_name);

            $student->user->profile->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'sex' => $request->sex,
                'birthdate' => $request->birthdate,
                'contact_number' => $request->contact_number,
                'guardian_name' => $guardianName,
                'address' => $request->address,
            ]);
        }

        $enrollment = Enrollment::create([
            'student_id'     => $request->student_id,
            'section_id'     => $request->section_id,
            'school_year_id' => $activeSY->id,
            'status'         => 'Enrolled', 
        ]);

        Student::where('id', $request->student_id)->update(['section_id' => $request->section_id]);

        // Update section adviser if provided
        if ($request->filled('adviser_id')) {
            $section = Section::findOrFail($request->section_id);
            $section->update(['adviser_id' => $request->adviser_id]);
        }

        $studentName = $enrollment->student->user->profile->full_name ?? 'N/A';
        $this->logActivity('Enroll Student', "Enrolled student {$studentName}");

        return back()->with('success', 'Student enrolled successfully!');
    }

    public function updateEnrollment(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $activeSY = SchoolYear::where('status', 'active')->first();
        if (!$activeSY) {
            return back()->with('error', 'Cannot modify records. No active school year.');
        }

        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        if ($request->school_year_id != $activeSY->id) {
            return back()->with('error', 'This school year is closed. No updates allowed.');
        }

        $exists = Enrollment::where('student_id', $enrollment->student_id)
            ->where('school_year_id', $request->school_year_id)
            ->where('id', '!=', $id) 
            ->exists();
        if ($exists) {
            return back()->withErrors(['student_id' => 'This student is already enrolled in the selected school year.']);
        }

        $section = Section::findOrFail($request->section_id);
        if ($section->capacity) {
            $count = Enrollment::where('section_id', $section->id)
                ->where('school_year_id', $request->school_year_id)
                ->count();

            if ($count >= $section->capacity && $enrollment->section_id != $section->id) {
                return back()->withErrors(['section_id' => 'This section has reached maximum capacity.']);
            }
        }

        $enrollment->update([
            'section_id'     => $request->section_id,
            'school_year_id' => $request->school_year_id,
        ]);

        Student::where('id', $enrollment->student_id)->update(['section_id' => $request->section_id]);

        $this->logActivity('Update Enrollment', "Updated enrollment ID {$id}");

        return back()->with('success', 'Enrollment updated successfully!');
    }

    public function destroyEnrollment($id)
    {
        $activeSY = SchoolYear::where('status', 'active')->first();
        if (!$activeSY) {
            return back()->with('error', 'Cannot delete records. School year is closed.');
        }

        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        $this->logActivity('Delete Enrollment', "Deleted enrollment ID {$id}");

        return back()->with('success', 'Enrollment record deleted.');
    }

    public function archive($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update(['archived' => true]);

        return redirect()->back()->with('success', 'Enrollment successfully archived.');
    }

    public function archivedList(Request $request)
    {
        $search = $request->input('search');

        $archived = Enrollment::where('archived', true)
            ->when($search, function ($q) use ($search) {
                $q->whereHas('student.user.profile', function ($sub) use ($search) {
                    $sub->where('lastname', 'like', "%$search%")
                        ->orWhere('firstname', 'like', "%$search%");
                });
            })
            ->with(['student.user.profile', 'section', 'schoolYear'])
            ->paginate(10);

        return view('registrars.enrollment_archived', compact('archived', 'search'));
    }

    public function restore($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update(['archived' => false]);

        return redirect()->back()->with('success', 'Enrollment successfully restored.');
    }


    
    public function verifyEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        
        $enrollment->update(['status' => 'Enrolled']); 
        
        $this->logActivity('Verify Enrollment', "Verified enrollment ID {$id}");

        return back()->with('success', 'Enrollment verified successfully.');
    }

    public function exportCsv()
    {
        return Excel::download(new EnrollmentsExport, 'enrollments.csv');
    }

    public function addStudent(Request $request)
    {
        $currentSchoolYear = SchoolYear::where('status', 'active')->first();
        if (!$currentSchoolYear) {
            return back()->withErrors(['school_year' => 'No active school year found or all are closed. Actions are disabled.']);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'sex' => 'required|string',
            'birthdate' => 'required|date',
            'contact_number' => 'required|string|max:20',
            'guardian_first_name' => 'required|string|max:255',
            'guardian_middle_name' => 'nullable|string|max:255',
            'guardian_last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
            'adviser_id' => 'nullable|exists:users,id',
        ]);

        $section = Section::findOrFail($request->section_id);
        $gradeLevelId = $section->gradelevel_id;
        $gradeLevelName = $section->gradeLevel->name ?? 'this grade level';

        // Check if selected section has capacity
        $selectedSectionFull = false;
        if ($section->capacity) {
            $count = Enrollment::where('section_id', $section->id)
                ->where('school_year_id', $currentSchoolYear->id)
                ->count();
            if ($count >= $section->capacity) {
                $selectedSectionFull = true;
            }
        }

        // If selected section is full, try to find another section with the same grade level
        if ($selectedSectionFull) {
            $availableSection = $this->findAvailableSection($gradeLevelId, $currentSchoolYear->id, $section->id);
            
            if (!$availableSection) {
                // All sections of this grade level are full
                return back()->withErrors([
                    'section_id' => "All sections for {$gradeLevelName} are full. Cannot enroll more students in {$gradeLevelName}."
                ]);
            }

            // Use the available section instead
            $section = $availableSection;
            $request->merge(['section_id' => $section->id]);
        }

        $baseCode = '400655';
        $lastStudent = Student::orderBy('id', 'desc')->first();
        $lastNumber = $lastStudent && preg_match('/^400655(\d{4,})$/', $lastStudent->student_number, $m)
            ? (int) $m[1] : 0;
        $studentNumber = $baseCode . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        $email = strtolower($request->last_name . '.' . $request->first_name . '@mindware.edu.ph');
        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'A student with this name already exists.']);
        }

        DB::transaction(function () use ($request, $currentSchoolYear, $studentNumber, $email, $section) {
            $lastFour = substr($studentNumber, -4);
            $tempPassword = ucfirst($request->last_name) . ucfirst($request->first_name) . $lastFour;

            $user = User::create([
                'email' => $email,
                'password' => Hash::make($tempPassword),
                'role_id' => 4,
                'status' => 'active',
            ]);

            // Concatenate guardian name fields
            $guardianName = trim($request->guardian_first_name . ' ' . 
                ($request->guardian_middle_name ? $request->guardian_middle_name . ' ' : '') . 
                $request->guardian_last_name);

            UserProfile::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'sex' => $request->sex,
                'birthdate' => $request->birthdate, 
                'address' => $request->address,
                'contact_number' => $request->contact_number,
                'guardian_name' => $guardianName,
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'student_number' => $studentNumber,
                'section_id' => $section->id,
            ]);

            Enrollment::create([
                'student_id' => $student->id,
                'section_id' => $section->id,
                'school_year_id' => $currentSchoolYear->id,
                'status' => 'Enrolled',
            ]);

            // Update section adviser if provided
            if ($request->filled('adviser_id')) {
                $section->update(['adviser_id' => $request->adviser_id]);
            }
        });

        return back()->with('success', 'Student successfully added and enrolled! Student Number: ' . $studentNumber);
    }

    public function exportPdf()
    {
        $enrollments = Enrollment::with(['student.user.profile', 'section', 'schoolYear'])->get();

        $pdf = Pdf::loadView('exports.enrollment-pdf', compact('enrollments'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('enrollments.pdf');
    }

    /**
     * Documents and Certificates
     */
    public function documentsAndCertificates(Request $request)
    {
        $currentSY = SchoolYear::where('status', 'active')->first();

        if (!$currentSY) {
            $empty = new LengthAwarePaginator(
                collect([]),
                0,
                10,
                LengthAwarePaginator::resolveCurrentPage(),
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            $students = Student::with('user.profile')->get();

            return view('registrars.documents-certificates', [
                'documents' => $empty,
                'certificates' => $empty,
                'students' => $students,
                'currentSY' => null,
            ]);
        }

        $documentQuery = StudentDocument::with(['student.user.profile'])
            ->where('school_year_id', $currentSY->id)
            ->latest();

        if ($request->filled('search') && $request->input('tab') !== 'certificates') {
            $search = $request->search;
            $documentQuery->whereHas('student.user.profile', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%");
            });
        }
        $documents = $documentQuery->paginate(10, ['*'], 'doc_page')->withQueryString();

        $certificateQuery = StudentCertificate::with(['student.user.profile'])
            ->where('school_year_id', $currentSY->id)
            ->latest();
        $students = Student::with('user.profile')->get();
        if ($request->filled('search') && $request->input('tab') === 'certificates') {
            $search = $request->search;
            $certificateQuery->whereHas('student.user.profile', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%");
            });
        }
        $certificates = $certificateQuery->paginate(10, ['*'], 'cert_page')->withQueryString();

        return view('registrars.documents-certificates', compact('documents', 'certificates', 'students', 'currentSY'));
    }

    public function storeDocument(Request $request, $studentId)
    {
        $currentSY = $this->getCurrentSY();
        if (!$currentSY) {
            return back()->withErrors(['msg' => 'Cannot upload documents. No active school year.']);
        }

        $request->validate([
            'type' => 'required|string|max:100',
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $student = Student::findOrFail($studentId);
        $path = $request->file('file')->store('student_documents', 'public');

        StudentDocument::create([
            'student_id' => $student->id,
            'school_year_id' => $currentSY->id,
            'type' => $request->type,
            'file_path' => $path,
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function viewDocuments($studentId)
    {
        $student = Student::with('user.profile')->findOrFail($studentId);
        $currentSY = $this->getCurrentSY();
        $documents = StudentDocument::where('student_id', $studentId)
            ->when($currentSY, fn($q) => $q->where('school_year_id', $currentSY->id))
            ->latest()
            ->get();

        return view('registrars.documents', compact('student', 'documents', 'currentSY'));
    }

    public function verifyDocument($id)
    {
        $doc = StudentDocument::findOrFail($id);
        $doc->update(['status' => 'Verified']);
        return back()->with('success', 'Document marked as verified.');
    }

    public function destroyDocument($id)
    {
        $doc = StudentDocument::findOrFail($id);

        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();
        return back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Grade Submissions
     */
    public function grades(Request $request)
    {
        $currentSY = SchoolYear::where('status', 'active')->first();

        if (!$currentSY) {
            $empty = new LengthAwarePaginator(
                collect([]),
                0,
                10,
                LengthAwarePaginator::resolveCurrentPage(),
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            return view('registrars.grades', [
                'assignments' => $empty,
                'currentSY'   => null,
            ]);
        }

        $query = SubjectAssignment::with(['teacher.profile', 'subject', 'section'])
            ->where('school_year_id', $currentSY->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('teacher', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%");
                })
                ->orWhereHas('subject', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%");
                });
            });
        }

        $assignments = $query->orderByDesc('updated_at')->paginate(10)->withQueryString();

        return view('registrars.grades', compact('assignments', 'currentSY'));
    }


    public function gradeSubmissions(Request $request)
    {
        $currentSY = SchoolYear::where('status', 'active')->first();
        $emptySummary = [
            'submitted' => 0,
            'approved'  => 0,
            'returned'  => 0,
            'total'     => 0,
        ];

        if (!$currentSY) {
            $empty = new LengthAwarePaginator(
                collect([]),
                0,
                10,
                LengthAwarePaginator::resolveCurrentPage(),
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            return view('registrars.grade-submissions', [
                'submissions' => $empty,
                'summary'     => $emptySummary,
                'currentSY'   => null,
            ]);
        }

        $query = DB::table('subject_assignments')
            ->join('subjects', 'subject_assignments.subject_id', '=', 'subjects.id')
            ->join('sections', 'subject_assignments.section_id', '=', 'sections.id')
            ->join('users', 'subject_assignments.teacher_id', '=', 'users.id')
            ->select(
                'subject_assignments.id',
                'subjects.name as subject_name',
                'sections.name as section_name',
                'subject_assignments.grade_status',
                'users.name as teacher_name'
            )
            ->where('subject_assignments.school_year_id', $currentSY->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subjects.name', 'like', "%{$search}%")
                  ->orWhere('users.name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('subject_assignments.grade_status', $request->status);
        }

        $submissions = $query->orderBy('subjects.name', 'asc')->paginate(10)->withQueryString();

        $summary = [
            'submitted' => DB::table('subject_assignments')
                ->where('school_year_id', $currentSY->id)
                ->where('grade_status', 'submitted')
                ->count(),
            'approved'  => DB::table('subject_assignments')
                ->where('school_year_id', $currentSY->id)
                ->where('grade_status', 'approved')
                ->count(),
            'returned'  => DB::table('subject_assignments')
                ->where('school_year_id', $currentSY->id)
                ->where('grade_status', 'returned')
                ->count(),
            'total'     => DB::table('subject_assignments')
                ->where('school_year_id', $currentSY->id)
                ->count(),
        ];

        return view('registrars.grade-submissions', compact('submissions', 'summary', 'currentSY'));
    }

    public function viewSubmission($id)
    {
        $assignment = SubjectAssignment::with(['teacher', 'subject', 'section'])
            ->findOrFail($id);

        $subject = $assignment->subject;
        $section = $assignment->section;

        $students = $section->students()
            ->with(['user.profile', 'grades' => function ($q) use ($subject) {
                $q->where('subject_id', $subject->id);
            }])
            ->get();

        $currentSY = SchoolYear::where('status', 'active')->first();
        if (!$currentSY || $assignment->school_year_id !== $currentSY->id) {
            return redirect()->route('registrars.grades')->with('error', 'Cannot view submission for an inactive school year.');
        }

        return view('registrars.grade-view', compact('assignment', 'subject', 'section', 'students', 'currentSY'));
    }


    public function updateStatus(Request $request, $id)
    {
        $currentSY = SchoolYear::where('status', 'active')->first();
        if (!$currentSY) {
            return back()->with('error', 'Cannot perform this action. No active school year.');
        }

        $assignment = DB::table('subject_assignments')->where('id', $id)->first();
        if (!$assignment) {
            return back()->with('error', 'Assignment not found.');
        }

        if ($assignment->school_year_id !== $currentSY->id) {
            return back()->with('error', 'Cannot update grades outside the active school year.');
        }

        $status = $request->input('status');
        $currentQuarter = SystemHelper::getActiveQuarter();

        if ($status === 'approved') {
            $hasGrades = DB::table('grades')
                ->where('subject_id', $assignment->subject_id)
                ->where('quarter', $currentQuarter)
                ->whereIn('student_id', function ($q) use ($assignment) {
                    $q->select('id')->from('students')->where('section_id', $assignment->section_id);
                })
                ->whereNotNull('grade')
                ->whereRaw('grade REGEXP "^[0-9]+(\\.[0-9]{1,2})?$"')
                ->exists();

            if (!$hasGrades) {
                return back()->with('error', 'Cannot approve. No grades submitted for this quarter.');
            }

            DB::table('grades')
                ->where('subject_id', $assignment->subject_id)
                ->where('quarter', $currentQuarter)
                ->whereIn('student_id', function ($q) use ($assignment) {
                    $q->select('id')->from('students')->where('section_id', $assignment->section_id);
                })
                ->update(['locked' => true]);

            DB::table('subject_assignments')
                ->where('id', $id)
                ->update([
                    'grade_status' => 'approved',
                    'updated_at' => now(),
                ]);

            $this->logActivity('Approve Grades', "Approved {$currentQuarter} quarter grades for assignment #{$id}.");
            $message = "Quarter {$currentQuarter} grades approved successfully!";
        } else {
            DB::table('grades')
                ->where('subject_id', $assignment->subject_id)
                ->where('quarter', $currentQuarter)
                ->whereIn('student_id', function ($q) use ($assignment) {
                    $q->select('id')->from('students')->where('section_id', $assignment->section_id);
                })
                ->update(['locked' => false]);

            DB::table('subject_assignments')
                ->where('id', $id)
                ->update([
                    'grade_status' => 'returned',
                    'updated_at' => now(),
                ]);

            $this->logActivity('Return Grades', "Returned {$currentQuarter} quarter grades for assignment #{$id} to teacher.");
            $message = "Quarter {$currentQuarter} grades returned to teacher for revision.";
        }

        return back()->with('success', $message);
    }

    public function returnGrades($assignment_id)
    {
        $currentSY = SchoolYear::where('status', 'active')->first();
        if (!$currentSY) {
            return back()->with('error', 'Cannot perform this action. No active school year.');
        }

        $assignment = DB::table('subject_assignments')->where('id', $assignment_id)->first();
        if (!$assignment) {
            return back()->with('error', 'Assignment not found.');
        }

        if ($assignment->school_year_id !== $currentSY->id) {
            return back()->with('error', 'Cannot update grades outside the active school year.');
        }

        DB::table('subject_assignments')
            ->where('id', $assignment_id)
            ->update([
                'grade_status' => 'returned',
                'updated_at' => now(),
            ]);

        $this->logActivity('Return Grades', "Returned grades for {$assignment->id} to teacher for revision.");

        return back()->with('info', 'Grades returned to teacher for revision.');
    }


    public function quarterSettings()
    {
        $currentSY = SchoolYear::where('status', 'active')->first();
        $activeQuarter = SystemHelper::getActiveQuarter();

        return view('registrars.quarter-settings', compact('activeQuarter', 'currentSY'));
    }

    public function updateQuarter(Request $request)
    {
        $request->validate([
            'quarter' => 'required|integer|min:1|max:4',
        ]);

        SystemHelper::setActiveQuarter($request->quarter);

        // When switching to a new quarter, reset grade workflows for the active school year
        $currentSY = SchoolYear::where('status', 'active')->first();
        if ($currentSY) {
            DB::table('subject_assignments')
                ->where('school_year_id', $currentSY->id)
                ->update([
                    'grade_status' => 'draft',
                    'updated_at'   => now(),
                ]);
        }

        $this->logActivity('Update Quarter', "Set active grading quarter to Q{$request->quarter} and reset grade workflows.");

        return back()->with('success', 'Active quarter updated successfully! All subject loads have been reset to Draft for the new quarter.');
    }

    /**
     * Sections
     */
    public function sections(Request $request)
    {
        $currentSY = SchoolYear::where('status', 'active')->first();

        $gradeLevels = GradeLevel::all();
        $teachers    = User::where('role_id', 3)->with('profile')->get();
        $subjects    = Subject::with('gradeLevel')
            ->when($currentSY, function ($q) use ($currentSY) {
                $q->where('school_year_id', $currentSY->id);
            })
            ->where('is_archived', false)
            ->orderBy('grade_level_id')
            ->orderBy('name')
            ->get();
        $reusableSchoolYears = SchoolYear::query()
            ->when($currentSY, fn($q) => $q->where('id', '!=', $currentSY->id))
            ->orderBy('start_date', 'desc')
            ->get();

        $assignedAdviserIds = $currentSY
            ? Section::where('school_year_id', $currentSY->id)
                ->whereNotNull('adviser_id')
                ->pluck('adviser_id')
                ->toArray()
            : [];

        $availableAdvisers = $teachers->filter(function ($teacher) use ($assignedAdviserIds) {
            return !in_array($teacher->id, $assignedAdviserIds);
        })->values();

        if (!$currentSY) {
            $emptyCollection = collect([]);
            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 10;
            $paginator = new LengthAwarePaginator(
                $emptyCollection->forPage($page, $perPage),
                $emptyCollection->count(),
                $perPage,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            return view('registrars.sections', [
                'sections'    => $paginator,
                'gradeLevels' => $gradeLevels,
                'teachers'    => $teachers,
                'subjects'    => $subjects,
                'currentSY'   => null,
                'reusableSchoolYears' => $reusableSchoolYears,
                'allSectionsForDropdown' => collect(),
                'availableAdvisers' => $availableAdvisers,
                'sectionSubjectAssignments' => [],
            ]);
        }

        $query = Section::with(['gradeLevel', 'schoolYear', 'adviser.profile'])
                        ->withCount(['enrollments' => function ($q) {
                            $q->where('archived', false);
                        }])
                        ->where('school_year_id', $currentSY->id);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhereHas('adviser.profile', function($sub) use ($request) {
                    $sub->where('first_name', 'like', '%' . $request->search . '%')
                        ->orWhere('last_name', 'like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->filled('gradelevel_id')) {
            $query->where('gradelevel_id', $request->gradelevel_id);
        }

        $sections = $query->paginate(10)->withQueryString();

        $allSectionsForDropdown = Section::where('school_year_id', $currentSY->id)
            ->orderBy('gradelevel_id')
            ->orderBy('name')
            ->with(['adviser'])
            ->get();

        // Get current subject assignments for each section (for edit modal)
        $sectionSubjectAssignments = [];
        // Get teachers assigned to each subject (for filtering dropdowns)
        $subjectTeachers = [];
        if ($currentSY) {
            $assignments = DB::table('subject_teacher')
                ->join('sections', 'subject_teacher.section_id', '=', 'sections.id')
                ->where('sections.school_year_id', $currentSY->id)
                ->select('subject_teacher.section_id', 'subject_teacher.subject_id', 'subject_teacher.teacher_id')
                ->get()
                ->groupBy('section_id');
            
            foreach ($assignments as $sectionId => $sectionAssignments) {
                $sectionSubjectAssignments[$sectionId] = $sectionAssignments
                    ->keyBy('subject_id')
                    ->map(fn($a) => $a->teacher_id);
            }

            // Get all teachers assigned to each subject (across all sections in active SY)
            $allSubjectAssignments = DB::table('subject_teacher')
                ->join('sections', 'subject_teacher.section_id', '=', 'sections.id')
                ->where('sections.school_year_id', $currentSY->id)
                ->select('subject_teacher.subject_id', 'subject_teacher.teacher_id')
                ->distinct()
                ->get()
                ->groupBy('subject_id');

            foreach ($allSubjectAssignments as $subjectId => $teacherAssignments) {
                $subjectTeachers[$subjectId] = $teacherAssignments->pluck('teacher_id')->toArray();
            }
        }

        return view('registrars.sections', compact(
            'sections',
            'gradeLevels',
            'teachers',
            'subjects',
            'currentSY',
            'reusableSchoolYears',
            'allSectionsForDropdown',
            'availableAdvisers',
            'sectionSubjectAssignments',
            'subjectTeachers'
        ));
    }


    public function storeSection(Request $request)
    {
        $currentSY = SchoolYear::where('status', 'active')->firstOrFail();

        $request->validate([
            'name'          => 'required|string|max:100',
            'gradelevel_id' => 'required|exists:grade_levels,id',
            'adviser_id'    => 'nullable|exists:users,id',
            'capacity'      => 'required|integer|min:1|max:30',
        ]);

        // If an adviser is selected, clear any existing advisory for this adviser
        // in the active school year so they only handle ONE section.
        if ($request->filled('adviser_id')) {
            Section::where('school_year_id', $currentSY->id)
                ->where('adviser_id', $request->adviser_id)
                ->update(['adviser_id' => null]);
        }

        Section::create([
            'name'           => $request->name,
            'gradelevel_id'  => $request->gradelevel_id,
            'school_year_id' => $currentSY->id,
            'adviser_id'     => $request->adviser_id,
            'capacity'       => $request->capacity ?? 30,
        ]);

        $this->logActivity('Add Section', "Added section {$request->name}");

        return back()->with('success', 'Section added successfully.');
    }

    public function reuseSections(Request $request)
    {
        $currentSY = SchoolYear::where('status', 'active')->first();
        if (!$currentSY) {
            return back()->with('error', 'Cannot reuse sections without an active school year.');
        }

        $request->validate([
            'source_school_year_id' => ['required', 'exists:school_years,id', 'different:' . $currentSY->id],
            'include_subject_assignments' => ['nullable', 'boolean'],
        ]);

        $sourceSY = SchoolYear::findOrFail($request->source_school_year_id);

        $sourceSections = Section::withTrashed()
            ->where('school_year_id', $sourceSY->id)
            ->get();

        if ($sourceSections->isEmpty()) {
            return back()->with('info', "No sections found for {$sourceSY->name}.");
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($sourceSections, $currentSY, $request, &$created, &$updated) {
            foreach ($sourceSections as $section) {
                $newSection = Section::withTrashed()->updateOrCreate(
                    [
                        'name' => $section->name,
                        'gradelevel_id' => $section->gradelevel_id,
                        'school_year_id' => $currentSY->id,
                    ],
                    [
                        'adviser_id' => $section->adviser_id,
                        'capacity' => $section->capacity,
                        'deleted_at' => null,
                    ]
                );

                $newSection->wasRecentlyCreated ? $created++ : $updated++;

                if ($request->boolean('include_subject_assignments')) {
                    $assignments = SubjectAssignment::where('section_id', $section->id)->get();

                    foreach ($assignments as $assignment) {
                        SubjectAssignment::updateOrCreate(
                            [
                                'section_id' => $newSection->id,
                                'subject_id' => $assignment->subject_id,
                                'school_year_id' => $currentSY->id,
                            ],
                            [
                                'teacher_id' => $assignment->teacher_id,
                                'grade_status' => 'draft',
                            ]
                        );
                    }
                }
            }
        });

        $this->logActivity('Reuse Sections', "Imported sections from {$sourceSY->name} to {$currentSY->name}");

        return back()->with('success', "Sections reused successfully. Created {$created}, updated {$updated}.");
    }

    public function updateSection(Request $request, $id)
    {
        $originalSection = Section::findOrFail($id);
        $currentSY = SchoolYear::where('status', 'active')->firstOrFail();

        $selectedSectionId = $request->input('selected_section_id', $id);
        $sectionToUpdate   = Section::findOrFail($selectedSectionId);

        $request->validate([
            'selected_section_id' => [
                'required',
                Rule::exists('sections', 'id')->where(fn ($q) => $q->where('school_year_id', $currentSY->id)),
            ],
            'adviser_id'    => 'nullable|exists:users,id',
            'capacity'      => 'required|integer|min:1|max:30',
            'subject_teachers' => 'nullable|array',
            'subject_teachers.*' => 'nullable|exists:users,id',
        ]);

        // If the user selected a different section, clear the adviser from the original section
        if ($sectionToUpdate->id !== $originalSection->id) {
            $originalSection->update(['adviser_id' => null]);
        }

        // Ensure only one advisory per school year
        if ($request->filled('adviser_id')) {
            Section::where('school_year_id', $sectionToUpdate->school_year_id)
                ->where('adviser_id', $request->adviser_id)
                ->where('id', '!=', $sectionToUpdate->id)
                ->update(['adviser_id' => null]);
        }

        $sectionToUpdate->update([
            'adviser_id'    => $request->adviser_id,
            'capacity'      => $request->capacity,
        ]);

        // Handle subject-teacher assignments
        $gradeLevelId = $sectionToUpdate->gradelevel_id;
        $subjectsForGrade = Subject::where('grade_level_id', $gradeLevelId)
            ->where('school_year_id', $currentSY->id)
            ->where('is_archived', false)
            ->get();

        // Get valid teacher IDs to filter out any invalid submissions
        $validTeacherIds = User::where('role_id', 3)
            ->where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'inactive');
            })
            ->pluck('id')
            ->toArray();

        foreach ($subjectsForGrade as $subject) {
            $teacherId = $request->input("subject_teachers.{$subject->id}");
            
            // Only process if teacher ID is valid
            if ($teacherId && in_array($teacherId, $validTeacherIds)) {
                // Insert into subject_teacher pivot table
                DB::table('subject_teacher')->updateOrInsert(
                    [
                        'section_id' => $sectionToUpdate->id,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'teacher_id' => $teacherId,
                        'updated_at' => now(),
                        'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                    ]
                );

                // Sync SubjectAssignment
                SubjectAssignment::updateOrCreate(
                    [
                        'teacher_id'     => $teacherId,
                        'section_id'     => $sectionToUpdate->id,
                        'subject_id'     => $subject->id,
                        'school_year_id' => $currentSY->id,
                    ],
                    [
                        'grade_status'   => 'draft',
                    ]
                );
            } else {
                // Remove assignment if teacher is cleared
                DB::table('subject_teacher')
                    ->where('section_id', $sectionToUpdate->id)
                    ->where('subject_id', $subject->id)
                    ->delete();

                SubjectAssignment::where('section_id', $sectionToUpdate->id)
                    ->where('subject_id', $subject->id)
                    ->where('school_year_id', $currentSY->id)
                    ->delete();
            }
        }

        $this->logActivity('Update Section', "Updated section {$sectionToUpdate->name}");

        return back()->with('success', 'Section updated successfully.');
    }

    public function archiveSection($id)
    {
        $section = Section::findOrFail($id);
        $section->delete();

        $this->logActivity('Archive Section', "Archived section {$section->name}");

        return back()->with('success', 'Section archived successfully.');
    }

    public function archivedSections()
    {
        $sections = Section::onlyTrashed()
            ->with(['gradeLevel', 'schoolYear', 'adviser.profile'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('registrars.sections_archived', compact('sections'));
    }

    public function restoreSection($id)
    {
        $section = Section::onlyTrashed()->findOrFail($id);
        $section->restore();

        $this->logActivity('Restore Section', "Restored section {$section->name}");

        return redirect()->route('registrars.sections.archived')->with('success', 'Section restored successfully!');
    }

    public function sectionSubjects($id)
    {
        $section = Section::findOrFail($id);

        $teachers = User::where('role_id', 3)->get();

        $sectionGradeLevelId = $section->gradelevel_id;

        $availableSubjects = Subject::where('grade_level_id', $sectionGradeLevelId)->get();

        $sectionSubjects = SubjectAssignment::where('section_id', $section->id)
                                             ->with(['subject', 'teacher.profile'])
                                             ->get();

        return view('registrars.subject_load', compact(
            'section', 
            'teachers', 
            'availableSubjects', 
            'sectionSubjects'
        ));
    }

    public function storeSectionSubject(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
        ]);
        
        $exists = SubjectAssignment::where('section_id', $section->id)
                                     ->where('subject_id', $request->subject_id)
                                     ->exists();
        
        if ($exists) {
            return back()->with('error', 'This subject is already assigned to this section.');
        }

        SubjectAssignment::create([
            'section_id' => $section->id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'school_year_id' => $section->school_year_id, 
        ]);

        return back()->with('success', 'Subject teacher assigned successfully!');
    }

    public function destroySectionSubject($sectionId, $subjectAssignmentId)
    {
        $assignment = SubjectAssignment::where('id', $subjectAssignmentId)
                                        ->where('section_id', $sectionId) 
                                        ->firstOrFail();

        $assignment->delete();

        return back()->with('success', 'Subject assignment removed successfully.');
    }


    public function classList($id)
    {
        $section = Section::with(['gradeLevel', 'adviser.profile', 'enrollments.student.user.profile'])->findOrFail($id);
        $students = $section->enrollments->map(fn($enr) => $enr->student->user);

        return view('registrars.classlist', compact('section', 'students'));
    }

    public function exportClassListPDF($id)
    {
        $section = Section::with([
            'gradeLevel',
            'adviser.profile',
            'enrollments.student.user.profile'
        ])->findOrFail($id);

        $schoolName = \App\Models\Setting::where('key', 'school_name')->value('value') ?? "Children's Mindware School Inc.";
        $schoolAddress = \App\Models\Setting::where('key', 'school_address')->value('value') ?? "Mindware Campus";
        $students = $section->enrollments->map(fn($e) => $e->student->user);

        $pdf = Pdf::loadView('exports.classlist-pdf', compact('section', 'students', 'schoolName', 'schoolAddress'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("ClassList_{$section->name}.pdf");
    }

    public function storeCertificate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|string|max:100',
            'remarks' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
        ]);

        $student = Student::with('profile')->findOrFail($request->student_id);

        if (!$student->profile) {
            return back()->with('error', 'This student has no linked user profile.');
        }

        $currentSY = $this->getCurrentSY();
        if (!$currentSY) {
            return back()->withErrors(['msg' => 'Cannot issue certificates. No active school year.']);
        }

        $certificate = StudentCertificate::create([
            'student_id' => $student->id,
            'school_year_id' => $currentSY->id,
            'type' => $request->type,
            'remarks' => $request->remarks,
            'purpose' => $request->purpose,
            'issued_by' => Auth::id(),
        ]);

        $issuer = Auth::user();
        $registrarName = trim(optional($issuer->profile)->first_name . ' ' . optional($issuer->profile)->last_name);
        $registrarName = $registrarName ?: 'The Registrar';

        $pdf = PDF::loadView('exports.certificates.completion', [
            'student' => $student,
            'certificate' => $certificate,
            'schoolName' => "Children's Mindware School Inc.",
            'schoolAddress' => 'Balagtas, Bulacan',
            'registrarName' => $registrarName,
        ])->setPaper('a4', 'portrait');

        $safeType = Str::slug($request->type);
        $path = "certificates/{$student->id}/{$safeType}-{$certificate->id}.pdf";

        Storage::disk('public')->put($path, $pdf->output());
        $certificate->update(['file_path' => $path]);

        return back()->with('success', 'Certificate issued successfully.');
    }


    public function destroyCertificate($id) 
    {
        $certificate = StudentCertificate::find($id);

        if (!$certificate) {
            return back()->with('error', 'Certificate not found.');
        }

        if ($certificate->file_path && \Storage::exists('public/' . $certificate->file_path)) {
            \Storage::delete('public/' . $certificate->file_path);
        }

        $certificate->delete();

        return back()->with('success', 'Certificate deleted successfully.');
    }

    public function generateCertificatePDF(StudentCertificate $certificate)
    {
        $certificate->load(['student.profile', 'issuer.profile']);
        $student = $certificate->student;

        if (!$student || !$student->profile) {
            return back()->with('error', 'This student has no linked record or user profile.');
        }

        $issuer = $certificate->issuer;
        $registrarName = optional($issuer->profile)
            ? trim($issuer->profile->first_name . ' ' . $issuer->profile->last_name)
            : 'The Registrar';

        $pdf = Pdf::loadView('exports.certificates.completion', [
            'student' => $student,
            'certificate' => $certificate,
            'schoolName' => "Children's Mindware School Inc.",
            'schoolAddress' => 'Balagtas, Bulacan',
            'registrarName' => $registrarName,
        ])->setPaper('a4', 'portrait');

        $type = $certificate->type ?? 'Certificate';
        $lastName = optional($student->profile)->last_name ?? 'Student';

        return $pdf->download("{$type}-{$lastName}.pdf");
    }


    /**
     * Teachers
     */
    public function teachers(Request $request)
    {
        $search    = $request->input('search');
        $currentSY = SchoolYear::where('status', 'active')->first();

        // Active / non-archived teachers
        $teachers = User::where('role_id', 3)
            ->where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'inactive');
            })
            ->with('profile')
            ->when($search, function ($query, $search) {
                $query->whereHas('profile', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('middle_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%");
                })->orWhere('email', 'like', "%$search%");
            })
            ->paginate(10);

        // Sections (active SY only)
        $sections = $currentSY
            ? Section::with('gradeLevel')
                ->where('school_year_id', $currentSY->id)
                ->orderBy('gradelevel_id')
                ->orderBy('name')
                ->get()
            : collect();

        // Subjects should come from the current curriculum for the active school year
        $subjects = collect();
        if ($currentSY) {
            // Pick the curriculum defined for this school year (if multiple, take the latest one)
            $currentCurriculum = Curriculum::where('school_year_id', $currentSY->id)
                ->where('is_template', false)
                ->orderByDesc('id')
                ->first();

            if ($currentCurriculum) {
                $subjects = $currentCurriculum->subjects()
                    ->with('gradeLevel')
                    ->where('is_archived', false)
                    ->orderBy('grade_level_id')
                    ->orderBy('name')
                    ->get();
            }
        }

        $gradeLevels = GradeLevel::pluck('name', 'id');

        // Advisory sections per teacher (active SY)
        $advisoriesByTeacher = $currentSY
            ? Section::where('school_year_id', $currentSY->id)
                ->whereNotNull('adviser_id')
                ->with('gradeLevel')
                ->get()
                ->groupBy('adviser_id')
            : collect();

        // Teaching load per teacher from subject_teacher pivot (active SY)
        $teachingLoads = $currentSY
            ? \DB::table('subject_teacher')
                ->join('sections', 'subject_teacher.section_id', '=', 'sections.id')
                ->join('subjects', 'subject_teacher.subject_id', '=', 'subjects.id')
                ->whereNull('sections.deleted_at')
                ->where('sections.school_year_id', $currentSY->id)
                ->select(
                    'subject_teacher.teacher_id',
                    'sections.id as section_id',
                    'sections.name as section_name',
                    'sections.gradelevel_id',
                    'subjects.id as subject_id',
                    'subjects.name as subject_name'
                )
                ->orderBy('sections.gradelevel_id')
                ->orderBy('sections.name')
                ->orderBy('subjects.name')
                ->get()
                ->groupBy('teacher_id')
            : collect();

        return view('registrars.teachers', compact(
            'teachers',
            'currentSY',
            'sections',
            'subjects',
            'gradeLevels',
            'advisoriesByTeacher',
            'teachingLoads'
        ));
    }

    /**
     * Archived teachers (status = inactive)
     */
    public function archivedTeachers(Request $request)
    {
        $search = $request->input('search');

        $teachers = User::where('role_id', 3)
            ->where('status', 'inactive')
            ->with('profile')
            ->when($search, function ($query, $search) {
                $query->whereHas('profile', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('middle_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%");
                })->orWhere('email', 'like', "%$search%");
            })
            ->paginate(10);

        return view('registrars.teachers_archived', compact('teachers'));
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:50',
            'middle_name'    => 'nullable|string|max:50',
            'last_name'      => 'required|string|max:50',
            'email'          => 'required|email|unique:users,email',
            'contact_number' => 'nullable|string|max:20',
        ]);

        $teacher = User::create([
            'name'     => $request->first_name . ' ' . $request->last_name,
            'email'    => $request->email,
            'password' => bcrypt('password123'),
            'role_id'  => 3, 
        ]);

        $teacher->profile()->create([
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'contact_number' => $request->contact_number,
        ]);

        $this->logActivity('Add Teacher', "Added teacher {$teacher->name}");

        return back()->with('success', 'Teacher added successfully.');
    }

    public function updateTeacher(Request $request, $id)
    {
        $teacher = User::where('role_id', 3)->findOrFail($id);

        $request->validate([
            'first_name'     => 'required|string|max:50',
            'middle_name'    => 'nullable|string|max:50',
            'last_name'      => 'required|string|max:50',
            'email'          => ['required', 'email', Rule::unique('users','email')->ignore($teacher->id)],
            'contact_number' => 'nullable|string|max:20',
        ]);

        $teacher->update([
            'name'  => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
        ]);

        if ($request->has('reset_password')) {
            $teacher->update(['password' => bcrypt('password123')]);
        }

        $teacher->profile()->updateOrCreate(
            ['user_id' => $teacher->id],
            [
                'first_name'     => $request->first_name,
                'middle_name'    => $request->middle_name,
                'last_name'      => $request->last_name,
                'contact_number' => $request->contact_number,
            ]
        );

        $this->logActivity('Update Teacher', "Updated teacher {$teacher->name}");

        return back()->with('success', 'Teacher updated successfully.');
    }

    public function destroyTeacher($id)
    {
        $teacher = User::where('role_id', 3)->findOrFail($id);

        $teacherName = $teacher->name;

        // Archive instead of hard delete
        $teacher->update(['status' => 'inactive']);

        $this->logActivity('Archive Teacher', "Archived teacher {$teacherName}");

        return back()->with('success', 'Teacher archived successfully.');
    }

    /**
     * Restore (unarchive) a teacher from archived list.
     */
    public function restoreTeacher($id)
    {
        $teacher = User::where('role_id', 3)
            ->where('status', 'inactive')
            ->findOrFail($id);

        $teacher->update(['status' => 'active']);

        $this->logActivity('Restore Teacher', "Restored teacher {$teacher->name}");

        return back()->with('success', 'Teacher restored successfully.');
    }

    public function showTeacher($id)
    {
        $teacher = User::where('role_id', 3)->with('profile')->findOrFail($id);
        return response()->json($teacher);
    }

    /**
     * Assign advisory section and teaching load to teacher (from Teacher Management).
     */
    public function assignTeacherLoad(Request $request, $id)
    {
        $teacher = User::where('role_id', 3)->findOrFail($id);
        $currentSY = SchoolYear::where('status', 'active')->first();

        $request->validate([
            'advisory_section_id'   => 'nullable|exists:sections,id',
            'teaching_subject_ids'  => 'nullable|array',
            'teaching_subject_ids.*'=> 'exists:subjects,id',
        ]);

        // Assign advisory section (update existing section record in the active SY)
        if ($currentSY && $request->filled('advisory_section_id')) {
            $selectedSection = Section::where('id', $request->advisory_section_id)
                ->where('school_year_id', $currentSY->id)
                ->firstOrFail();

            if ($selectedSection->adviser_id && $selectedSection->adviser_id != $teacher->id) {
                return back()->withErrors([
                    'advisory_section_id' => 'This section already has an assigned adviser.',
                ])->withInput();
            }

            // Clear previous advisory of this teacher in active SY (other sections)
            Section::where('school_year_id', $currentSY->id)
                ->where('adviser_id', $teacher->id)
                ->where('id', '!=', $selectedSection->id)
                ->update(['adviser_id' => null]);

            // Set adviser for the selected section
            $selectedSection->update(['adviser_id' => $teacher->id]);
        }

        // Assign teaching subjects to the advisory section (if set)
        if ($currentSY && $request->filled('advisory_section_id') && $request->filled('teaching_subject_ids')) {
            $sectionId = (int) $request->advisory_section_id;

            foreach ($request->teaching_subject_ids as $subjectId) {
                $subjectId = (int) $subjectId;

                // Always INSERT a new teaching load row for this teacher + section + subject (for class list / load views)
                \DB::table('subject_teacher')->insert([
                    'section_id' => $sectionId,
                    'subject_id' => $subjectId,
                    'teacher_id' => $teacher->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 🔁 Keep SubjectAssignment in sync so the Grades page dropdown sees the same load
                SubjectAssignment::updateOrCreate(
                    [
                        'teacher_id'     => $teacher->id,
                        'section_id'     => $sectionId,
                        'subject_id'     => $subjectId,
                        'school_year_id' => $currentSY->id,
                    ],
                    [
                        'grade_status'   => 'draft',
                    ]
                );
            }
        }

        $this->logActivity('Assign Teacher Load', "Updated advisory/teaching load for teacher {$teacher->name}");

        return back()->with('success', 'Teacher advisory and teaching load updated.');
    }

    public function assignSubject(Request $request, Section $section)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $teacher = User::findOrFail($request->teacher_id);

        if ($teacher->role_id != 3) {
            return back()->withErrors(['teacher_id' => 'Selected user is not a teacher.']);
        }

        \DB::table('subject_teacher')->updateOrInsert(
            [
                'section_id' => $section->id,
                'subject_id' => $request->subject_id,
            ],
            [
                'teacher_id' => $request->teacher_id,
                'updated_at' => now(),
                'created_at' => now(), 
            ]
        );

        $this->logActivity('Assign Subject', "Assigned subject ID {$request->subject_id} to teacher ID {$request->teacher_id} for section {$section->id}");

        return back()->with('success', 'Subject assigned to teacher successfully.');
    }

    /**
     * Reports
     */
    public function reports(Request $request)
    {
        $activeSY = SchoolYear::where('status', 'active')->first();
        $schoolYearId = $request->get('school_year_id', $activeSY?->id);

        $schoolYears = SchoolYear::orderBy('id', 'desc')->get();

        $selectedSY = SchoolYear::find($schoolYearId);
        $syClosed = !$activeSY || $activeSY->status === 'closed'; 

        // Enrollment summary
        $totalEnrolled = Enrollment::where('school_year_id', $schoolYearId)->count();
        $maleCount = Enrollment::whereHas('student.user.profile', fn($q) => $q->where('sex', 'Male'))
            ->where('school_year_id', $schoolYearId)->count();
        $femaleCount = Enrollment::whereHas('student.user.profile', fn($q) => $q->where('sex', 'Female'))
            ->where('school_year_id', $schoolYearId)->count();

        // Enrollment by Grade Level
        $byGradeLevel = Enrollment::select('grade_levels.name as grade', DB::raw('COUNT(enrollments.id) as total'))
            ->join('sections', 'sections.id', '=', 'enrollments.section_id')
            ->join('grade_levels', 'grade_levels.id', '=', 'sections.gradelevel_id')
            ->where('enrollments.school_year_id', $schoolYearId)
            ->groupBy('grade_levels.name')
            ->orderBy('grade_levels.name')
            ->get();

        return view('registrars.reports', compact(
            'schoolYears',
            'schoolYearId',
            'activeSY',
            'selectedSY',
            'syClosed',
            'totalEnrolled',
            'maleCount',
            'femaleCount',
            'byGradeLevel'
        ));
    }

    public function exportReportsPDF(Request $request)
    {
        $activeSY = SchoolYear::where('status', 'active')->first();

        if (!$activeSY || $activeSY->status === 'closed') {
            return back()->with('error', 'The school year is closed. Report export is disabled.');
        }

        $schoolYearId = $request->get('school_year_id');
        $schoolYear = SchoolYear::find($schoolYearId);

        $totalEnrolled = Enrollment::where('school_year_id', $schoolYearId)->count();
        $maleCount = Enrollment::whereHas('student.user.profile', fn($q) => $q->where('sex', 'Male'))
            ->where('school_year_id', $schoolYearId)->count();
        $femaleCount = Enrollment::whereHas('student.user.profile', fn($q) => $q->where('sex', 'Female'))
            ->where('school_year_id', $schoolYearId)->count();

        $byGradeLevel = Enrollment::select('grade_levels.name as grade', DB::raw('COUNT(enrollments.id) as total'))
            ->join('sections', 'sections.id', '=', 'enrollments.section_id')
            ->join('grade_levels', 'grade_levels.id', '=', 'sections.gradelevel_id')
            ->where('enrollments.school_year_id', $schoolYearId)
            ->groupBy('grade_levels.name')
            ->orderBy('grade_levels.name')
            ->get();

        $pdf = Pdf::loadView('exports.reports-summary-pdf', compact(
            'schoolYear',
            'totalEnrolled',
            'maleCount',
            'femaleCount',
            'byGradeLevel'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('Enrollment_Report_'.$schoolYear->name.'.pdf');
    }


    /**
     * Settings
     */
    public function settings()
    {
        $registrar = Auth::user();
        return view('registrars.settings', compact('registrar'));
    }

    public function updateSettings(Request $request)
    {
        $registrar = Auth::user();

        $validated = $request->validate([
            'first_name'      => 'required|string|max:100',
            'middle_name'     => 'nullable|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => ['required', 'email', Rule::unique('users', 'email')->ignore($registrar->id)],
            'contact_number'  => 'nullable|string|max:20',
            'sex'             => 'nullable|in:Male,Female',
            'birthdate'       => 'nullable|date|before:today',
            'address'         => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $registrar->update(['email' => $validated['email']]);

        $profile = $registrar->profile ?? $registrar->profile()->create();

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $profile->profile_picture = $path;
        }

        $profile->first_name     = $validated['first_name'];
        $profile->middle_name    = $validated['middle_name'] ?? null;
        $profile->last_name      = $validated['last_name'];
        $profile->contact_number = $validated['contact_number'] ?? null;
        $profile->sex            = $validated['sex'] ?? null;
        $profile->birthdate      = $validated['birthdate'] ?? null;
        $profile->address        = $validated['address'] ?? null;
        $profile->save();

        $this->logActivity('Update Profile', "Updated profile: {$registrar->email}");

        return back()->with('success', 'Settings updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $registrar = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (!Hash::check($request->current_password, $registrar->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $registrar->update(['password' => Hash::make($request->new_password)]);

        $this->logActivity('Change Password', "Changed password for {$registrar->email}");

        return back()->with('success', 'Password changed successfully!');
    }

    protected function getCurrentSY()
    {
        return SchoolYear::where('status', 'active')->first();
    }

    public function subjects(Request $request)
    {
        $currentSY = SchoolYear::where('status', 'active')->first();

        $gradeLevels = GradeLevel::all();
        $reusableSchoolYears = SchoolYear::query()
            ->when($currentSY, fn($q) => $q->where('id', '!=', $currentSY->id))
            ->orderBy('start_date', 'desc')
            ->get();

        if (!$currentSY) {
            $empty = collect([]);
            $subjects = new LengthAwarePaginator(
                $empty,
                0,
                10,
                LengthAwarePaginator::resolveCurrentPage(),
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            return view('registrars.subjects', compact('subjects', 'gradeLevels', 'currentSY', 'reusableSchoolYears'));
        }

        $subjects = Subject::with('gradeLevel')
            ->where('is_archived', false)
            ->where('school_year_id', $currentSY->id)
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->filled('grade_level_id'), fn($q) => $q->where('grade_level_id', $request->grade_level_id))
            ->orderBy('grade_level_id')
            ->paginate(10)
            ->withQueryString();

        return view('registrars.subjects', compact('subjects', 'gradeLevels', 'currentSY', 'reusableSchoolYears'));
    }

    public function storeSubject(Request $request)
    {
        $currentSY = $this->getCurrentSY();
        if (!$currentSY) {
            return back()->withErrors(['msg' => 'Cannot add subjects. No active school year.']);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('subjects')->where(function ($q) use ($request, $currentSY) {
                    return $q->where('grade_level_id', $request->grade_level_id)
                        ->where('school_year_id', $currentSY->id);
                }),
            ],
            'grade_level_id' => 'required|exists:grade_levels,id',
        ]);

        Subject::create([
            'name' => $request->name,
            'grade_level_id' => $request->grade_level_id,
            'school_year_id' => $currentSY->id,
            'is_archived' => false,
        ]);

        $this->logActivity('Add Subject', "Added subject {$request->name}");
        return back()->with('success', 'Subject added.');
    }

    public function reuseSubjects(Request $request)
    {
        $currentSY = $this->getCurrentSY();
        if (!$currentSY) {
            return back()->withErrors(['msg' => 'Cannot reuse subjects without an active school year.']);
        }

        $request->validate([
            'source_school_year_id' => ['required', 'exists:school_years,id', 'different:' . $currentSY->id],
        ]);

        $sourceSY = SchoolYear::findOrFail($request->source_school_year_id);

        $sourceSubjects = Subject::where('school_year_id', $sourceSY->id)
            ->where('is_archived', false)
            ->get();

        if ($sourceSubjects->isEmpty()) {
            return back()->with('info', "No active subjects found for {$sourceSY->name}.");
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($sourceSubjects, $currentSY, &$created, &$updated) {
            foreach ($sourceSubjects as $subject) {
                $clone = Subject::updateOrCreate(
                    [
                        'name' => $subject->name,
                        'grade_level_id' => $subject->grade_level_id,
                        'school_year_id' => $currentSY->id,
                    ],
                    [
                        'description' => $subject->description,
                        'is_archived' => false,
                    ]
                );

                $clone->wasRecentlyCreated ? $created++ : $updated++;
            }
        });

        $this->logActivity('Reuse Subjects', "Imported subjects from {$sourceSY->name} to {$currentSY->name}");

        return back()->with('success', "Subjects reused successfully. Created {$created}, updated {$updated}.");
    }

    public function updateSubject(Request $request, $id)
    {
        $currentSY = $this->getCurrentSY();
        if (!$currentSY) {
            return back()->withErrors(['msg' => 'Cannot edit subjects. SY is closed.']);
        }

        $subject = Subject::findOrFail($id);
        if ($subject->school_year_id !== $currentSY->id) {
            return back()->withErrors(['msg' => 'This subject belongs to a different school year.']);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('subjects')->where(function ($q) use ($request, $currentSY) {
                    return $q->where('grade_level_id', $request->grade_level_id)
                        ->where('school_year_id', $currentSY->id);
                })->ignore($subject->id),
            ],
            'grade_level_id' => 'required|exists:grade_levels,id',
        ]);

        $subject->update([
            'name' => $request->name,
            'grade_level_id' => $request->grade_level_id,
        ]);

        $this->logActivity('Update Subject', "Updated subject {$subject->name}");
        return back()->with('success', 'Subject updated.');
    }

    public function archiveSubject($id)
    {
        $currentSY = $this->getCurrentSY();
        if (!$currentSY) {
            return back()->withErrors(['msg' => 'Cannot archive subjects. SY is closed.']);
        }

        $subject = Subject::findOrFail($id);
        if ($subject->school_year_id !== $currentSY->id) {
            return back()->withErrors(['msg' => 'Cannot archive a subject from another school year.']);
        }
        $subject->update(['is_archived' => true]);

        $this->logActivity('Archive Subject', "Archived subject {$subject->name}");
        return back()->with('success', 'Subject archived.');
}

    public function archivedSubjects(Request $request)
    {
        $subjects = Subject::with('gradeLevel')
            ->where('is_archived', true)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('grade_level_id'), function ($q) use ($request) {
                $q->where('grade_level_id', $request->grade_level_id);
            })
            ->orderBy('grade_level_id')
            ->paginate(10);

        $gradeLevels = GradeLevel::all();

        return view('registrars.subjects-archived', compact('subjects', 'gradeLevels'));
    }

    public function restoreSubject($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->update(['is_archived' => false]);

        $this->logActivity('Restore Subject', "Restored subject {$subject->name}");

        return back()->with('success', 'Subject restored successfully.');
    }

    public function curriculum(Request $request)
    {
        $currentSY = SchoolYear::where('status', 'active')->first();

        $curricula = collect();
        $subjectsByGrade = collect();
        
        if ($currentSY) {
            $curricula = Curriculum::with(['schoolYear', 'subjects.gradeLevel'])
                ->where('school_year_id', $currentSY->id)
                ->where('is_template', false)
                ->orderBy('name')
                ->get();
            
            // Get all subjects for the active school year, grouped by grade level
            $subjectsByGrade = Subject::where('school_year_id', $currentSY->id)
                ->where('is_archived', false)
                ->with('gradeLevel')
                ->get()
                ->groupBy('grade_level_id');
        }

        // All defined curricula (global list for Set Curriculum dropdown)
        // Collapse duplicates by name so each curriculum name appears only once.
        $allCurricula = Curriculum::with('subjects.gradeLevel')
            ->where('is_template', false)
            ->orderBy('name')
            ->get()
            ->groupBy('name')
            ->map(function ($group) {
                // Prefer a curriculum whose school_year_id is null (template),
                // otherwise just take the first one in the group.
                return $group->firstWhere('school_year_id', null) ?? $group->first();
            })
            ->values();

        // Get reusable school years (for reuse functionality)
        $reusableSchoolYears = SchoolYear::query()
            ->when($currentSY, fn($q) => $q->where('id', '!=', $currentSY->id))
            ->orderBy('start_date', 'desc')
            ->get();

        $canReuseCurricula = $currentSY && $reusableSchoolYears->isNotEmpty();

        return view('registrars.curriculum', compact(
            'curricula',
            'subjectsByGrade',
            'reusableSchoolYears',
            'canReuseCurricula',
            'currentSY',
            'allCurricula'
        ));
    }

    public function storeCurriculum(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'school_year_id' => 'required|exists:school_years,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $schoolYearId = (int) $request->school_year_id;

        // Ensure the selected school year already has subjects and sections defined.
        $hasSubjects = Subject::where('school_year_id', $schoolYearId)
            ->where('is_archived', false)
            ->exists();

        $hasSections = Section::where('school_year_id', $schoolYearId)->exists();

        if (!$hasSubjects || !$hasSections) {
            return back()
                ->withInput()
                ->withErrors([
                    'school_year_id' => 'Cannot add a curriculum for this school year because it has no subjects and/or sections defined yet. Please create subjects and sections first.',
                ]);
        }

        $curriculum = Curriculum::create([
            'name' => $request->name,
            'school_year_id' => $schoolYearId,
            'is_template' => false,
        ]);

        $curriculum->subjects()->attach($request->subjects);

        $this->logActivity('Add Curriculum', "Added curriculum {$request->name}");

        return redirect()->route('registrars.curriculum', ['school_year_id' => $request->school_year_id])
            ->with('success', 'Curriculum added successfully.');
    }

    /**
     * Apply an existing curriculum to the selected school year (inherit subjects).
     */
    public function applyCurriculum(Request $request)
    {
        $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'curriculum_id' => 'required|exists:curricula,id',
        ]);

        $schoolYearId = (int) $request->school_year_id;

        $source = Curriculum::with('subjects')->findOrFail($request->curriculum_id);

        // Create a new curriculum for this school year with the same name
        $curriculum = Curriculum::create([
            'name' => $source->name,
            'school_year_id' => $schoolYearId,
            'is_template' => false,
        ]);

        // Inherit all subjects from the selected curriculum
        $subjectIds = $source->subjects->pluck('id')->all();
        if (!empty($subjectIds)) {
            $curriculum->subjects()->sync($subjectIds);
        }

        $this->logActivity('Set Curriculum', "Applied curriculum {$source->name} to school year ID {$schoolYearId}");

        return redirect()->route('registrars.curriculum', ['school_year_id' => $schoolYearId])
            ->with('success', 'Curriculum applied and subjects inherited successfully.');
    }

    public function showCurriculum($id)
    {
        $curriculum = Curriculum::with(['schoolYear', 'subjects.gradeLevel'])
            ->findOrFail($id);

        // Group subjects by grade level
        $subjectsByGradeLevel = $curriculum->subjects()
            ->with('gradeLevel')
            ->get()
            ->groupBy(function ($subject) {
                return $subject->gradeLevel->name ?? 'Unassigned';
            })
            ->map(function ($subjects) {
                return $subjects->map(function ($subject) {
                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                    ];
                });
            });

        return view('registrars.curriculum-show', compact('curriculum', 'subjectsByGradeLevel'));
    }

    public function updateCurriculum(Request $request, $id)
    {
        $curriculum = Curriculum::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $curriculum->update([
            'name' => $request->name,
        ]);

        $curriculum->subjects()->sync($request->subjects);

        $this->logActivity('Update Curriculum', "Updated curriculum {$curriculum->name}");

        return redirect()->route('registrars.curriculum', ['school_year_id' => $curriculum->school_year_id])
            ->with('success', 'Curriculum updated successfully.');
    }

    public function destroyCurriculum($id)
    {
        $curriculum = Curriculum::findOrFail($id);
        $curriculumName = $curriculum->name;
        $schoolYearId = $curriculum->school_year_id;

        $curriculum->delete();

        $this->logActivity('Delete Curriculum', "Deleted curriculum {$curriculumName}");

        return redirect()->route('registrars.curriculum', ['school_year_id' => $schoolYearId])
            ->with('success', 'Curriculum deleted successfully.');
    }

    public function getSubjectsForCurriculum(Request $request)
    {
        $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        $subjects = Subject::where('school_year_id', $request->school_year_id)
            ->where('is_archived', false)
            ->with('gradeLevel')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'grade_level_id' => $subject->grade_level_id,
                    'grade_level_name' => $subject->gradeLevel->name ?? 'Unassigned',
                ];
            });

        return response()->json(['subjects' => $subjects]);
    }

    public function getCurriculaForSchoolYear(Request $request)
    {
        $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        $curricula = Curriculum::where('school_year_id', $request->school_year_id)
            ->where('is_template', false)
            ->withCount('subjects')
            ->orderBy('name')
            ->get()
            ->map(function ($curriculum) {
                return [
                    'id' => $curriculum->id,
                    'name' => $curriculum->name,
                    'subjects_count' => $curriculum->subjects_count,
                ];
            });

        return response()->json(['curricula' => $curricula]);
    }

    public function reuseCurricula(Request $request)
    {
        $currentSY = $this->getCurrentSY();
        if (!$currentSY) {
            return back()->withErrors(['msg' => 'Cannot reuse curricula without an active school year.']);
        }

        $request->validate([
            'source_school_year_id' => ['required', 'exists:school_years,id', 'different:' . $currentSY->id],
        ]);

        $sourceSY = SchoolYear::findOrFail($request->source_school_year_id);

        $sourceCurricula = Curriculum::where('school_year_id', $sourceSY->id)
            ->with('subjects')
            ->get();

        if ($sourceCurricula->isEmpty()) {
            return back()->with('info', "No curricula found for {$sourceSY->name}.");
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($sourceCurricula, $currentSY, &$created, &$updated) {
            foreach ($sourceCurricula as $curriculum) {
                // Create or update curriculum in current school year
                $newCurriculum = Curriculum::updateOrCreate(
                    [
                        'name' => $curriculum->name,
                        'school_year_id' => $currentSY->id,
                    ]
                );

                $newCurriculum->wasRecentlyCreated ? $created++ : $updated++;

                // Map subjects from source school year to current school year
                // Match subjects by name and grade level
                $subjectIds = [];
                foreach ($curriculum->subjects as $sourceSubject) {
                    $matchingSubject = Subject::where('school_year_id', $currentSY->id)
                        ->where('name', $sourceSubject->name)
                        ->where('grade_level_id', $sourceSubject->grade_level_id)
                        ->where('is_archived', false)
                        ->first();

                    if ($matchingSubject) {
                        $subjectIds[] = $matchingSubject->id;
                    }
                }

                // Sync subjects to the new curriculum
                if (!empty($subjectIds)) {
                    $newCurriculum->subjects()->sync($subjectIds);
                }
            }
        });

        $this->logActivity('Reuse Curricula', "Imported curricula from {$sourceSY->name} to {$currentSY->name}");

        return back()->with('success', "Curricula reused successfully. Created {$created}, updated {$updated}.");
    }
}
