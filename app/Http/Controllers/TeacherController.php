<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Assignment;
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
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    // ---------------- DASHBOARD ----------------
    public function dashboard()
    {
        $teacher = Auth::user();

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
        ]);
    }

    // ---------------- ANNOUNCEMENTS ----------------
    public function announcements(Request $request)
{
    $teacher = Auth::user();

    // ---- My Announcements (posted by this teacher) ----
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

    // Paginate para consistent sa global tab
    $myAnnouncements = $myQuery->paginate(5, ['*'], 'my_page')->withQueryString();

    // ---- Global Announcements (from admin or other teachers) ----
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

    // ---- Sections for create form ----
    $sections = Section::with('gradeLevel')->get();

    return view('teachers.announcements', compact(
        'teacher',
        'sections',
        'myAnnouncements',
        'globalAnnouncements'
    ));
}


    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content'  => 'required|string',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        Announcement::create([
            'title'      => $request->title,
            'content'    => $request->content,
            'section_id' => $request->section_id ?? null,
            'user_id'    => Auth::id(),
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
        $advisorySection = Section::where('adviser_id', $teacher->id)->first();

        if (!$advisorySection) {
            return view('teachers.classlist', [
                'section' => null,
                'studentsMale' => collect(),
                'studentsFemale' => collect(),
                'mySubjects' => collect(),
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

        return view('teachers.classlist', compact('section', 'studentsMale', 'studentsFemale', 'mySubjects'));}

    public function exportClassList()
    {
        $teacher = Auth::user();
        $section = Section::where('adviser_id', $teacher->id)->first();

        if (!$section) {
            return back()->withErrors('You have no advisory section.');
        }

        $studentsMale = $section->students()->whereHas('profile', fn($q) => $q->where('sex', 'Male'))->get();
        $studentsFemale = $section->students()->whereHas('profile', fn($q) => $q->where('sex', 'Female'))->get();

        $pdf = Pdf::loadView('teachers.reports.classlist-pdf', compact('section', 'studentsMale', 'studentsFemale'));
        $this->logActivity('Export Class List', "Exported class list for section {$section->name}");

        return $pdf->download("{$section->name}_classlist.pdf");
    }

    // ---------------- GRADES ----------------
    public function grades(Request $request)
    {
        $teacher = Auth::user(); 
        $teacherId = (int) $teacher->id; 

        $assignments = DB::table('subject_assignments')
            ->join('subjects', 'subject_assignments.subject_id', '=', 'subjects.id')
            ->join('sections', 'subject_assignments.section_id', '=', 'sections.id')
            ->join('grade_levels', 'sections.gradelevel_id', '=', 'grade_levels.id')
            ->where('subject_assignments.teacher_id', $teacherId)
            ->select(
                'subject_assignments.id as assignment_id',
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                'sections.id as section_id',
                'sections.name as section_name',
                'grade_levels.name as gradelevel_name'
            )
            ->get();

        $selectedAssignment = null;
        $students = collect(); 
        $subject = null;
        $section = null;

        if ($request->filled('assignment_id')) {
            $assignmentId = (int) $request->assignment_id; 

            $selectedAssignment = $assignments->where('assignment_id', $assignmentId)->first();
            
            if (!$selectedAssignment) {
                return back()->withErrors(['assignment_id' => 'Invalid or unauthorized subject assignment selected.']);
            }

            $subject = Subject::find($selectedAssignment->subject_id);
            $section = Section::find($selectedAssignment->section_id);

            if ($section) {
                $students = $section->students()->with(['user.profile', 'grades' => function ($q) use ($subject) {
                    $q->where('subject_id', $subject->id);
                }])
                ->get()
                ->sortBy(function ($student) {
                    return $student->user->profile->last_name ?? 'ZZZ';
                });
            }
        }

        return view('teachers.grades', [
            'assignments'        => $assignments,
            'selectedAssignment' => $selectedAssignment, 
            'students'           => $students,             
            'subject'            => $subject,             
            'section'            => $section,             
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
        $teacherId = Auth::id(); // Keep the teacher ID for logging/auditing if needed

        foreach ($request->grades as $studentId => $quarters) {
            foreach ($quarters as $quarter => $gradeValue) {
                
                if (is_numeric($gradeValue) && $gradeValue >= 60 && $gradeValue <= 100) {
                    Grade::updateOrCreate(
                        [
                            'student_id' => (int)$studentId,
                            'subject_id' => (int)$request->subject_id,
                            'quarter'    => $quarter,
                        ],
                        [
                            // Aalisin ang teacher_id dito para maiwasan ang 'Unknown column' error
                            'grade'      => (int)$gradeValue,
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

        $this->logActivity('Save Grades', "Updated grades for {$savedCount} quarter entries in {$subject->name} ({$section->name}).");

        $assignment = DB::table('subject_assignments')
            ->where('teacher_id', $teacherId)
            ->where('subject_id', $request->subject_id)
            ->where('section_id', $request->section_id)
            ->first();
            
        $assignmentId = $assignment->id ?? null;

        return redirect()
            ->route('teachers.grades', ['assignment_id' => $assignmentId])
            ->with('success', 'Grades successfully saved/updated!');
    }


    protected function getTeacherAssignedStudents()
    {
        $teacherId = Auth::id();
        
        $subjectSectionIds = DB::table('subject_teacher')
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

        return view('teachers.reports', compact('gradeLevels', 'sections', 'schoolYears', 'students'));
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

        return view('teachers.reports', compact('students', 'gradeLevels', 'sections', 'schoolYears'));
    }

    public function exportReportsPDF(Request $request)
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
            'new_password'     => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
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
