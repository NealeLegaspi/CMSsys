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

        $announcements = Announcement::where('user_id', $teacher->id)
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

        $myAnnouncements = Announcement::where('user_id', $teacher->id)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(10);

        $globalAnnouncements = Announcement::with('user')
            ->whereNull('section_id')
            ->latest()
            ->paginate(10);

        $sections = Section::with('gradeLevel')->get();

        return view('teachers.announcements', compact('teacher', 'myAnnouncements', 'globalAnnouncements', 'sections'));
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
                'sectionName' => null,
                'studentsMale' => collect(),
                'studentsFemale' => collect(),
                'mySubjects' => collect(),
            ]);
        }

        $sectionId = $advisorySection->id;
        $sectionName = $advisorySection->name;

        $studentsMale = Enrollment::where('section_id', $sectionId)
            ->whereHas('student.profile', fn($q) => $q->where('sex', 'Male'))
            ->with('student.profile')
            ->get();

        $studentsFemale = Enrollment::where('section_id', $sectionId)
            ->whereHas('student.profile', fn($q) => $q->where('sex', 'Female'))
            ->with('student.profile')
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

        return view('teachers.classlist', compact('sectionName', 'studentsMale', 'studentsFemale', 'mySubjects'));
    }

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

        $subjects = DB::table('subject_teacher')
            ->join('subjects', 'subject_teacher.subject_id', '=', 'subjects.id')
            ->join('sections', 'subject_teacher.section_id', '=', 'sections.id')
            ->join('grade_levels', 'sections.gradelevel_id', '=', 'grade_levels.id')
            ->where('subject_teacher.teacher_id', $teacher->id)
            ->select(
                'subjects.id as subject_id',
                'grade_levels.name as gradelevel',
                'sections.name as section_name',
                'subjects.name as subject_name'
            )
            ->get();

        $selectedSubject = null;

        if ($request->filled('subject_id')) {
            $selectedSubject = Subject::with('section.gradeLevel')->find($request->subject_id);

            if ($selectedSubject) {
                $teaches = DB::table('subject_teacher')
                    ->where('teacher_id', $teacher->id)
                    ->where('subject_id', $selectedSubject->id)
                    ->exists();

                if (! $teaches) {
                    $selectedSubject = null;
                    return back()->withErrors(['subject_id' => 'You are not assigned to the selected subject.']);
                }
            }
        }

        return view('teachers.grades', compact('teacher', 'subjects', 'selectedSubject'));
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

        foreach ($request->grades as $studentId => $quarters) {
            foreach ($quarters as $quarter => $gradeValue) {
                if (!empty($gradeValue)) {
                    Grade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'subject_id' => $request->subject_id,
                            'quarter'    => $quarter,
                        ],
                        [
                            'grade'      => $gradeValue,
                            'teacher_id' => Auth::id(),
                        ]
                    );
                }
            }
        }

        $this->logActivity('Save Grades', "Saved grades for " . count($request->grades) . " students in {$request->subject_id}");

        return redirect()
            ->route('teachers.grades', ['subject_id' => $request->subject_id])
            ->with('success', 'Grades saved successfully!');
    }

    // ---------------- REPORTS ----------------
    public function reports()
    {
        $gradeLevels = GradeLevel::all();
        $sections = Section::with('gradeLevel')->get();
        $schoolYears = ['2022-2023', '2023-2024', '2024-2025'];
        $students = Student::with('user.profile', 'section.gradeLevel')->get();

        return view('teachers.reports', compact('gradeLevels', 'sections', 'schoolYears', 'students'));
    }

    public function filterReports(Request $request)
    {
        $students = Student::with('user.profile', 'section.gradeLevel')
            ->when($request->gradelevel_id, function ($q) use ($request) {
                $q->whereHas('section', function ($s) use ($request) {
                    $s->where('gradelevel_id', $request->gradelevel_id);
                });
            })
            ->when($request->section_id, function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            })
            ->when($request->school_year, function ($q) use ($request) {
                $q->where('school_year', $request->school_year);
            })
            ->get();

        $gradeLevels = GradeLevel::all();
        $sections = Section::with('gradeLevel')->get();
        $schoolYears = ['2022-2023', '2023-2024', '2024-2025'];

        return view('teachers.reports', compact('students', 'gradeLevels', 'sections', 'schoolYears'));
    }

    public function exportReportsPDF(Request $request)
    {
        $students = Student::with('user.profile', 'section.gradeLevel')
            ->when($request->gradelevel_id, function ($q) use ($request) {
                $q->whereHas('section', function ($s) use ($request) {
                    $s->where('gradelevel_id', $request->gradelevel_id);
                });
            })
            ->when($request->section_id, function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            })
            ->when($request->school_year, function ($q) use ($request) {
                $q->where('school_year', $request->school_year);
            })
            ->get();

        $pdf = Pdf::loadView('teachers.reports_pdf', compact('students'))
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
