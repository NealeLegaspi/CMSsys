<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\SubjectAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\GradeLevel;
use App\Models\ActivityLog;
use App\Models\SchoolYear;
use App\Helpers\SystemHelper;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Exports\GradeTemplateExport;
use App\Imports\GradeImport;


class TeacherController extends Controller
{
    // ---------------- DASHBOARD ----------------
    public function dashboard()
    {
        $teacher = Auth::user();
        $activeSY = SchoolYear::where('status', 'active')->first();

        if (!$activeSY) {
            return view('teachers.dashboard', [
                'noActiveSY' => true,
                'announcements' => collect(),
            ]);
        }

        $sections = Section::where('adviser_id', $teacher->id)->pluck('name', 'id')->toArray();
        $sectionCount = count($sections);

        $totals = [];
        $male = 0;
        $female = 0;

        foreach ($sections as $id => $name) {
            $totalStudents = Enrollment::where('section_id', $id)->count();
            $totals[] = $totalStudents;

            $male += Enrollment::where('section_id', $id)
                        ->whereHas('student.profile', fn($q) => $q->where('sex','Male'))->count();

            $female += Enrollment::where('section_id', $id)
                        ->whereHas('student.profile', fn($q) => $q->where('sex','Female'))->count();
        }

        $announcements = Announcement::with('user.profile')
            ->whereIn('target_type', ['Global', 'Teacher'])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->take(5)
            ->get();

        return view('teachers.dashboard', [
            'teacher'      => $teacher,
            'sectionCount' => $sectionCount,
            'sections'     => $sections,
            'totals'       => $totals,
            'genderLabels' => ['Male', 'Female'],
            'genderData'   => [$male, $female],
            'announcements'=> $announcements,
            'activeSY'     => $activeSY,
        ]);
    }


    // ---------------- ANNOUNCEMENTS ----------------
    public function announcements(Request $request)
    {
        $teacher = Auth::user();

        $currentSY = SchoolYear::where('status', 'active')->first();
        $syClosed = !$currentSY; 

        $myQuery = Announcement::where('user_id', $teacher->id)
            ->with(['section', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $myQuery->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('section_filter')) {
            $myQuery->where('section_id', $request->section_filter);
        }

        $myAnnouncements = $myQuery->paginate(5, ['*'], 'my_page')->withQueryString();

        $globalQuery = Announcement::with(['section', 'user.profile'])
            ->whereIn('target_type', ['Global', 'Teacher'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('content', 'like', '%' . $request->search . '%');
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
            })
            ->latest();

        $globalAnnouncements = $globalQuery->paginate(5, ['*'], 'global_page')->withQueryString();

        $sections = Section::with('gradeLevel')
            ->whereIn('id', function ($q) use ($teacher) {
                $q->select('section_id')
                ->from('subject_assignments')
                ->where('teacher_id', $teacher->id);
            })
            ->get();

        return view('teachers.announcements', compact(
            'teacher',
            'sections',
            'myAnnouncements',
            'globalAnnouncements',
            'currentSY',
            'syClosed'
        ));
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content'  => 'required|string',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $targetType = $request->section_id ? 'Student' : 'Global';

        Announcement::create([
            'title'      => $request->title,
            'content'    => $request->content,
            'section_id' => $request->section_id ?? null,
            'user_id'    => Auth::id(),
            'target_type' => $targetType,
        ]);

        $this->logActivity('Create Announcement', "Created announcement {$request->title}");

        return back()->with('success', 'Announcement created successfully!');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content'  => 'required|string',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $announcement->update([
            'title'      => $request->title,
            'content'    => $request->content,
            'section_id' => $request->section_id ?? null,
        ]);

        $this->logActivity('Update Announcement', "Updated announcement {$announcement->id}");

        return back()->with('success', 'Announcement updated successfully!');
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $announcement->delete();

        $this->logActivity('Delete Announcement', "Deleted announcement {$announcement->id}");

        return back()->with('success', 'Announcement deleted successfully!');
    }   

    // ---------------- CLASS LIST ----------------
    public function classlist()
    {
        $teacher = Auth::user();

        $currentSY = SchoolYear::where('status', 'active')->first();
        $syClosed = !$currentSY; 

        $advisorySection = Section::where('adviser_id', $teacher->id)->first();

        if (!$advisorySection) {
            return view('teachers.classlist', [
                'section' => null,
                'studentsMale' => collect(),
                'studentsFemale' => collect(),
                'mySubjects' => collect(),
                'currentSY' => $currentSY,
                'syClosed' => $syClosed,
            ]);
        }

        $section = $advisorySection;

        $baseQuery = Student::whereHas('activeEnrollment', fn($q) => $q->where('section_id', $section->id))
                        ->with('user.profile'); 

        $studentsMale = (clone $baseQuery)
            ->whereHas('user.profile', fn($q) => $q->where('sex', 'Male')->orderBy('last_name'))
            ->get();

        $studentsFemale = (clone $baseQuery)
            ->whereHas('user.profile', fn($q) => $q->where('sex', 'Female')->orderBy('last_name'))
            ->get();

        $mySubjects = DB::table('subject_teacher')
            ->join('subjects', 'subject_teacher.subject_id', '=', 'subjects.id')
            ->join('sections', 'subject_teacher.section_id', '=', 'sections.id')
            ->join('grade_levels', 'sections.gradelevel_id', '=', 'grade_levels.id')
            ->where('subject_teacher.teacher_id', $teacher->id)
            ->select(
                'grade_levels.name as gradelevel',
                'sections.name as section_name',
                'subjects.name as subject_name'
            )
            ->get();

        return view('teachers.classlist', compact(
            'section', 'studentsMale', 'studentsFemale', 'mySubjects', 'currentSY', 'syClosed'
        ));
    }


    public function exportClassList()
    {
        $currentSY = SchoolYear::where('status', 'active')->first();
        if (!$currentSY) {
            return back()->withErrors('Export is disabled because the School Year is closed.');
        }

        $teacher = Auth::user();
        $section = Section::where('adviser_id', $teacher->id)->first();

        if (!$section) {
            return back()->withErrors('You have no advisory section.');
        }

        $studentsMale = $section->students()
            ->whereHas('profile', fn($q) => $q->where('sex', 'Male'))
            ->with('profile')
            ->get();

        $studentsFemale = $section->students()
            ->whereHas('profile', fn($q) => $q->where('sex', 'Female'))
            ->with('profile')
            ->get();

        $pdf = Pdf::loadView('teachers.reports.classlist-pdf', [
            'section' => $section,
            'studentsMale' => $studentsMale,
            'studentsFemale' => $studentsFemale,
        ])->setPaper('A4', 'portrait');

        $this->logActivity('Export Class List', "Exported class list for section {$section->name}");

        return $pdf->download("{$section->name}_classlist.pdf");
    }

    // ---------------- GRADES ----------------
    public function grades(Request $request)
    {
        $teacherId = Auth::id();

        $currentSY = SchoolYear::where('status', 'active')->first();
        $syClosed = !$currentSY || $currentSY->status === 'closed';

        $assignments = SubjectAssignment::with(['subject', 'section.gradeLevel'])
            ->where('teacher_id', $teacherId)
            ->get()
            ->map(function ($a) {
                return (object)[
                    'assignment_id'  => $a->id,
                    'subject_id'     => $a->subject->id ?? null,
                    'subject_name'   => $a->subject->name ?? 'N/A',
                    'section_id'     => $a->section->id ?? null,
                    'section_name'   => $a->section->name ?? 'N/A',
                    'gradelevel_name'=> $a->section->gradeLevel->name ?? 'N/A',
                    'grade_status'   => $a->grade_status ?? 'draft',
                ];
            });

        $selectedAssignment = null;
        $students = collect();
        $subject = null;
        $section = null;
        $activeQuarter = SystemHelper::getActiveQuarter();
        $lockedQuarters = [];

        if ($request->filled('assignment_id')) {
            $assignmentId = (int) $request->assignment_id;

            $selectedAssignment = SubjectAssignment::with(['subject', 'section.gradeLevel'])
                ->where('teacher_id', $teacherId)
                ->find($assignmentId);

            if (!$selectedAssignment) {
                return back()->withErrors(['assignment_id' => 'Invalid or unauthorized subject assignment selected.']);
            }

            $subject = $selectedAssignment->subject;
            $section = $selectedAssignment->section;

            if ($section) {
                $students = $section->students()
                    ->with([
                        'user.profile',
                        'grades' => function ($q) use ($subject) {
                            $q->where('subject_id', $subject->id);
                        }
                    ])
                    ->get()
                    ->sortBy(fn($s) => $s->user->profile->last_name ?? 'ZZZ');

                $lockedQuarters = DB::table('grades')
                    ->where('subject_id', $subject->id)
                    ->whereIn('quarter', ['1st', '2nd', '3rd', '4th'])
                    ->where('locked', true)
                    ->distinct()
                    ->pluck('quarter')
                    ->toArray();
            }
        }

        return view('teachers.grades', [
            'assignments'        => $assignments,
            'selectedAssignment' => $selectedAssignment,
            'students'           => $students,
            'subject'            => $subject,
            'section'            => $section,
            'activeQuarter'      => $activeQuarter,
            'lockedQuarters'     => $lockedQuarters,
            'syClosed'           => $syClosed,
            'currentSY'          => $currentSY,
        ]);
    }

    public function encodeGrades($subjectId, $sectionId)
    {
        $subject = Subject::findOrFail($subjectId);
        $section = Section::findOrFail($sectionId);

        $students = $section->students()->with(['grades' => function ($q) use ($subject) {
            $q->where('subject_id', $subject->id);
        }])->get();

        $this->logActivity('Encode Grades', "Accessed grade encoding for {$section->name}");

        return view('teachers.grades.encode', compact('subject', 'section', 'students'));
    }

    public function editGrades(Subject $subject)
    {
        $section = $subject->section;

        $students = $section->students()->with(['grades' => function ($q) use ($subject) {
            $q->where('subject_id', $subject->id);
        }])->get();

        $this->logActivity('Edit Grades', "Edited grades for {$students->count()} students in {$subject->id}");

        return view('teachers.grades.edit', compact('subject', 'section', 'students'));
    }

    public function storeGrades(Request $request)
    {
        $activeSY = SchoolYear::where('status', 'active')->first();
        if (!$activeSY || $activeSY->status === 'closed') {
            return back()->with('error', 'The school year is closed. Grade encoding is disabled.');
        }

        $request->validate([
            'grades'     => 'required|array',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $subject = Subject::find($request->subject_id);
        $section = Section::find($request->section_id);

        if (!$subject || !$section) {
            return back()->with('error', 'Invalid Subject or Section data.');
        }

        $savedCount = 0;
        $teacherId = Auth::id();
        $currentQuarter = SystemHelper::getActiveQuarter();

        $assignment = DB::table('subject_assignments')
            ->where('teacher_id', $teacherId)
            ->where('subject_id', $request->subject_id)
            ->where('section_id', $request->section_id)
            ->first();

        if ($assignment && $assignment->grade_status === 'approved') {
            $locked = DB::table('grades')
                ->where('subject_id', $request->subject_id)
                ->where('quarter', $currentQuarter)
                ->whereIn('student_id', function ($q) use ($request) {
                    $q->select('id')->from('students')->where('section_id', $request->section_id);
                })
                ->where('locked', true)
                ->exists();

            if ($locked) {
                return back()->with('error', 'This quarter’s grades are locked and cannot be modified.');
            }
        }

        foreach ($request->grades as $studentId => $quarters) {
            foreach ($quarters as $quarter => $gradeValue) {

                if ((int)$quarter > $currentQuarter) continue;

                if (is_numeric($gradeValue) && $gradeValue >= 60 && $gradeValue <= 100) {
                    Grade::updateOrCreate(
                        [
                            'student_id' => (int)$studentId,
                            'subject_id' => (int)$request->subject_id,
                            'quarter'    => $quarter,
                        ],
                        [
                            'grade' => number_format((float)$gradeValue, 2, '.', ''), // ✔ FIX HERE
                        ]
                    );
                    $savedCount++;
                } 
                elseif (empty($gradeValue)) {
                    Grade::where('student_id', (int)$studentId)
                        ->where('subject_id', (int)$request->subject_id)
                        ->where('quarter', $quarter)
                        ->delete();
                }
            }
        }

        $this->logActivity('Save Grades', "Updated {$savedCount} grade entries for {$subject->name} ({$section->name}) in Q{$currentQuarter}.");

        if ($assignment && $assignment->grade_status === 'draft') {
            DB::table('subject_assignments')
                ->where('id', $assignment->id)
                ->update(['updated_at' => now()]);
        }

        return redirect()
            ->route('teachers.grades', ['assignment_id' => $assignment->id ?? null])
            ->with('success', "Grades successfully saved for Quarter {$currentQuarter}!");
    }


    public function submitGrades(Request $request)
    {
        $activeSY = SchoolYear::where('status', 'active')->first();
        if (!$activeSY || $activeSY->status === 'closed') {
            return back()->with('error', 'The school year is closed. Grade submission is disabled.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $teacherId = Auth::id();
        $currentQuarter = SystemHelper::getActiveQuarter();

        $assignment = DB::table('subject_assignments')
            ->where('teacher_id', $teacherId)
            ->where('subject_id', $request->subject_id)
            ->where('section_id', $request->section_id)
            ->first();

        if (!$assignment) {
            return back()->with('error', 'No subject assignment found for this section.');
        }

        if ($assignment->grade_status === 'approved') {
            return back()->with('error', 'Grades have already been approved and cannot be resubmitted.');
        }

        if ($assignment->grade_status === 'returned') {
            $hasUpdated = DB::table('grades')
                ->where('subject_id', $request->subject_id)
                ->where('quarter', $currentQuarter)
                ->whereExists(function ($query) use ($assignment) {
                    $query->select(DB::raw(1))
                        ->from('subject_assignments')
                        ->where('id', $assignment->id)
                        ->whereColumn('subject_assignments.updated_at', '<', 'grades.updated_at');
                })
                ->exists();

            if (!$hasUpdated) {
                return back()->with('error', 'You must update the returned grades before resubmitting.');
            }
        }

        $enrolledStudents = Enrollment::where('section_id', $request->section_id)
            ->where('status', 'enrolled')
            ->pluck('student_id');

        $gradedCount = Grade::whereIn('student_id', $enrolledStudents)
            ->where('subject_id', $request->subject_id)
            ->where('quarter', $currentQuarter)
            ->count();

        if ($gradedCount < $enrolledStudents->count()) {
            return back()->with('error', 'All enrolled students must have grades before submission.');
        }

        DB::table('subject_assignments')
            ->where('id', $assignment->id)
            ->update([
                'grade_status' => 'submitted',
                'updated_at' => now(),
            ]);

        $subject = Subject::find($request->subject_id);
        $section = Section::find($request->section_id);

        $this->logActivity('Submit Grades', "Submitted grades for {$subject->name} ({$section->name}) - Quarter {$currentQuarter}");

        return redirect()
            ->route('teachers.grades', ['assignment_id' => $assignment->id])
            ->with('success', "Grades successfully submitted for Quarter {$currentQuarter}. Pending registrar approval.");
    }

    public function downloadImportTemplate()
    {
        $content = "student_number,1st,2nd,3rd,4th\n";

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="grade_import_template.csv"');
    }

    public function importGrades(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $subjectId = $request->subject_id;
        $sectionId = $request->section_id;
        $teacherId = Auth::id();
        $currentQuarter = SystemHelper::getActiveQuarter();

        $assignment = DB::table('subject_assignments')
            ->where('teacher_id', $teacherId)
            ->where('subject_id', $subjectId)
            ->where('section_id', $sectionId)
            ->first();

        if (!$assignment) {
            return back()->with('error', 'Unauthorized subject assignment.');
        }

        // Check locked quarter
        $locked = DB::table('grades')
            ->where('subject_id', $subjectId)
            ->where('quarter', $currentQuarter)
            ->where('locked', true)
            ->exists();

        if ($locked) {
            return back()->with('error', 'This quarter is locked. You cannot import grades.');
        }

        $file = fopen($request->file('file'), 'r');
        $header = fgetcsv($file); // skip header row

        $rowsImported = 0;

        while (($row = fgetcsv($file)) !== false) {

            $studentNumber = $row[0] ?? null;

            $student = Student::where('student_number', $studentNumber)->first();

            if (!$student) {
                continue; 
            }

            $studentId = $student->id; 

            $q1 = $row[1] ?? null;
            $q2 = $row[2] ?? null;
            $q3 = $row[3] ?? null;
            $q4 = $row[4] ?? null;

            // Only update quarters up to active quarter
            $quarters = [
                '1st' => $q1,
                '2nd' => $q2,
                '3rd' => $q3,
                '4th' => $q4,
            ];

            foreach ($quarters as $quarter => $value) {

                // Don't allow future quarter
                if ($quarter === '2nd' && $currentQuarter < 2) continue;
                if ($quarter === '3rd' && $currentQuarter < 3) continue;
                if ($quarter === '4th' && $currentQuarter < 4) continue;

                if (is_numeric($value) && $value >= 60 && $value <= 100) {
                    Grade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'subject_id' => $subjectId,
                            'quarter' => $quarter
                        ],
                        [
                            'grade' => number_format((float)$value, 2, '.', '')
                        ]
                    );

                    $rowsImported++;
                }
            }
        }

        fclose($file);

        $this->logActivity(
            'Import Grades',
            "Imported {$rowsImported} grade entries for subject {$subjectId} section {$sectionId}"
        );

        return back()->with('success', "Successfully imported {$rowsImported} grade entries!");
    }



    protected function getTeacherAssignedStudents()
    {
        $teacherId = Auth::id();
        
        $subjectSectionIds = DB::table('subject_assignments')
                                ->where('teacher_id', $teacherId)
                                ->pluck('section_id');

        $advisorySectionId = Section::where('adviser_id', $teacherId)->pluck('id');
        
        $allSectionIds = $subjectSectionIds->merge($advisorySectionId)->unique();
        
        $studentIds = Enrollment::whereIn('section_id', $allSectionIds)
                                            ->pluck('student_id')
                                            ->unique();

        return Student::whereIn('id', $studentIds)
                      ->with(['user.profile', 'section.gradeLevel'])
                      ->get();
    }

    // ---------------- REPORTS ----------------
    public function reports()
    {
        $students = $this->getTeacherAssignedStudents();

        $gradeLevelIds = $students->pluck('activeEnrollment.section.gradelevel_id')
                            ->unique()
                            ->filter();
        $sectionIds = $students->pluck('activeEnrollment.section.id')
                            ->unique()
                            ->filter();

        $gradeLevels = GradeLevel::whereIn('id', $gradeLevelIds)->get();
        $sections = Section::whereIn('id', $sectionIds)->with('gradeLevel')->get();
        $schoolYears = SchoolYear::pluck('name')->toArray(); 

        $currentSY = SchoolYear::where('status', 'active')->first();
        $syClosed = !$currentSY || $currentSY->status === 'closed'; // 🔒 Detect closed SY

        return view('teachers.reports', compact('gradeLevels', 'sections', 'schoolYears', 'students', 'currentSY', 'syClosed'));
    }

    public function filterReports(Request $request)
    {
        $studentIds = $this->getTeacherAssignedStudents()->pluck('id');

        $query = Student::whereIn('id', $studentIds)
            ->with(['user.profile', 'activeEnrollment.section.gradeLevel']);

        $query->when($request->filled('gradelevel_id'), function ($q) use ($request) {
            $q->whereHas('section', function ($s) use ($request) {
                $s->where('gradelevel_id', $request->gradelevel_id);
            });
        });

        $query->when($request->filled('section_id'), function ($q) use ($request) {
            $q->where('section_id', $request->section_id);
        });

        $query->when($request->filled('school_year'), function ($q) use ($request) {
            $q->whereHas('enrollments.schoolYear', function ($e) use ($request) {
                $e->where('name', $request->school_year);
            });
        });

        $students = $query->get();

        $gradeLevelIds = $students->pluck('activeEnrollment.section.gradelevel_id')->unique()->filter();
        $sectionIds = $students->pluck('activeEnrollment.section_id')->unique()->filter();
        $gradeLevels = GradeLevel::whereIn('id', $gradeLevelIds)->get();
        $sections = Section::whereIn('id', $sectionIds)->with('gradeLevel')->get();
        $schoolYears = SchoolYear::pluck('name')->toArray(); 

        $currentSY = SchoolYear::where('status', 'active')->first();
        $syClosed = !$currentSY || $currentSY->status === 'closed'; // 🔒

        return view('teachers.reports', compact('students', 'gradeLevels', 'sections', 'schoolYears', 'currentSY', 'syClosed'));
    }

    public function exportReportsPDF(Request $request)
    {
        $currentSY = SchoolYear::where('status', 'active')->first();
        if (!$currentSY || $currentSY->status === 'closed') {
            return back()->with('error', 'The school year is closed. Report export is disabled.');
        }

        $studentIds = $this->getTeacherAssignedStudents()->pluck('id');

        $query = Student::whereIn('id', $studentIds)
            ->with(['user.profile', 'activeEnrollment.section.gradeLevel']);

        $query->when($request->filled('gradelevel_id'), function ($q) use ($request) {
            $q->whereHas('section', function ($s) use ($request) {
                $s->where('gradelevel_id', $request->gradelevel_id);
            });
        });

        $query->when($request->filled('section_id'), function ($q) use ($request) {
            $q->where('section_id', $request->section_id);
        });

        $query->when($request->filled('school_year'), function ($q) use ($request) {
            $q->whereHas('enrollments.schoolYear', function ($e) use ($request) {
                $e->where('name', $request->school_year);
            });
        });

        $students = $query->get();

        $pdf = \App::make('dompdf.wrapper'); 
        $pdf->loadView('teachers.reports_pdf', compact('students'))
                ->setPaper('a4', 'landscape');

        $this->logActivity('Export Reports', "Exported reports for {$students->count()} students");

        return $pdf->download('students_report.pdf');
    }


    // ---------------- SETTINGS ----------------
    public function settings()
    {
        $teacher = Auth::user();
        return view('teachers.settings', compact('teacher'));
    }

    public function updateSettings(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'first_name'       => ['required', 'string', 'max:100'],
            'middle_name'      => ['nullable', 'string', 'max:100'],
            'last_name'        => ['required', 'string', 'max:100'],
            'email'            => ['required', 'email', 'max:255', 'unique:users,email,' . $teacher->id],
            'contact_number'   => ['nullable', 'string', 'max:20'],
            'sex'              => ['nullable', 'in:Male,Female'],
            'birthdate'        => ['nullable', 'date', 'before:today'],
            'address'          => ['nullable', 'string', 'max:255'],
            'profile_picture'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        $teacher->update([
            'email' => $validated['email'],
        ]);

        $profile = $teacher->profile;
        if (!$profile) {
            $profile = $teacher->profile()->create([
                'profile_picture' => 'images/default.png',
            ]);
        }

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

        $this->logActivity('Update Profile', "Updated profile for {$teacher->email}");

        return back()->with('success', 'Settings updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (!Hash::check($request->current_password, $teacher->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $teacher->update([
            'password' => Hash::make($request->new_password),
        ]);

        $this->logActivity('Change Password', "Changed password for {$teacher->email}");

        return back()->with('success', 'Password changed successfully!');
    }

    protected function logActivity($action, $description)
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'description'=> $description,
        ]);
    }
}