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

        // Sections assigned to this teacher
        $sections = Section::where('adviser_id', $teacher->id)->pluck('name', 'id')->toArray();

        $sectionCount = count($sections);

        $totals = [];
        $male = 0;
        $female = 0;

        foreach ($sections as $id => $name) {
            // Count enrolled students in this section
            $totalStudents = Enrollment::where('section_id', $id)->count();
            $totals[] = $totalStudents;

            // Count gender distribution
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

        $query = Announcement::where('user_id', $teacher->id);

        // search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // filter
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $myAnnouncements = $query->latest()->paginate(5);

        $sections = Section::with('gradeLevel')->get();

        return view('teachers.announcements', compact('teacher', 'myAnnouncements', 'sections'));
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
            'content'    => $request->content,   // migration column
            'user_id'    => Auth::id(),
            'section_id' => $request->section_id ?? null,
        ]);

        return back()->with('success', 'Announcement posted successfully!');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content'  => 'required|string',
        ]);

        $announcement->update([
            'title'   => $request->title,
            'content' => $request->content, // use content column
        ]);

        return back()->with('success', 'Announcement updated successfully!');
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $announcement->delete();

        return back()->with('success', 'Announcement deleted successfully!');
    }

    // ---------------- ASSIGNMENTS ----------------
    public function assignments() {
        $assignments = Assignment::where('teacher_id', Auth::id())
            ->with('section.gradeLevel')
            ->latest()
            ->get();    

        $sections = Section::with('gradeLevel')->get(); 
        $subjects = Subject::all()->groupBy('grade_level_id');

        return view('teachers.assignments', compact('assignments', 'sections', 'subjects'));
    }

    public function storeAssignment(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        Assignment::create([
            'title' => $request->title,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => Auth::id(), // laging naka-bind sa logged-in teacher
        ]);

        return redirect()->route('teachers.assignments')->with('success', 'Assignment created.');
    }

    public function updateAssignment(Request $request, Assignment $assignment) {
        // ownership check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $assignment->update($request->only('title','instructions','due_date','section_id','subject_id'));

        return redirect()->route('teachers.assignments')->with('success', 'Assignment updated.');
    }

    public function destroyAssignment(Assignment $assignment) {
        // ownership check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $assignment->delete();
        return redirect()->route('teachers.assignments')->with('success', 'Assignment deleted.');
    }


    // ---------------- CLASS LIST ----------------
    public function classlist()
    {
        $teacher = Auth::user();

        // Advisory section
        $advisorySection = Section::where('adviser_id', $teacher->id)->first();

        $sectionId = $advisorySection->id ?? null;
        $sectionName = $advisorySection->name ?? null;

        $studentsMale = [];
        $studentsFemale = [];

        if ($advisorySection) {
            $studentsMale = Enrollment::where('section_id', $sectionId)
                ->whereHas('student.profile', fn($q) => $q->where('sex','Male'))
                ->with(['student', 'student.profile'])
                ->get()
                ->map(fn($enr) => (object)[
                    'lrn'         => $enr->student->lrn ?? 'N/A',
                    'first_name'  => $enr->student->profile->first_name ?? '',
                    'middle_name' => $enr->student->profile->middle_name ?? '',
                    'last_name'   => $enr->student->profile->last_name ?? '',
                    'status'      => $enr->student->status ?? 'inactive',
                ]);

            $studentsFemale = Enrollment::where('section_id', $sectionId)
                ->whereHas('student.profile', fn($q) => $q->where('sex','Male'))
                ->with(['student', 'student.profile'])
                ->get()
                ->map(fn($enr) => (object)[
                    'lrn'         => $enr->student->lrn ?? 'N/A',
                    'first_name'  => $enr->student->profile->first_name ?? '',
                    'middle_name' => $enr->student->profile->middle_name ?? '',
                    'last_name'   => $enr->student->profile->last_name ?? '',
                    'status'      => $enr->student->status ?? 'inactive',
                ]);
        }

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
            'sectionName',
            'sectionId',
            'studentsMale',
            'studentsFemale',
            'mySubjects'
        ));
    }

    public function exportClassList()
    {
        $teacher = Auth::user();

        $section = $teacher->advisorySection ?? null;
        $sectionId = $section->id ?? null;
        $sectionName = $section->name ?? null;

        $studentsMale = $section ? $section->students()->where('gender', 'male')->get() : [];
        $studentsFemale = $section ? $section->students()->where('gender', 'female')->get() : [];

        $pdf = Pdf::loadView('teachers.reports.classlist-pdf', compact(
            'sectionName',
            'studentsMale',
            'studentsFemale'
        ));

        return $pdf->download('classlist.pdf');
    }

    // ---------------- GRADES ----------------
    public function grades(Request $request)
    {
        $teacher = Auth::user();

        // kuha lahat ng subjects assigned kay teacher
        $subjects = Subject::whereHas('assignments', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->with('section')->get();

        // default values
        $subject = null;
        $section = null;
        $students = collect();

        // kapag may pinili sa dropdown
        if ($request->filled('subject_id')) {
            $subject = Subject::with('section')->findOrFail($request->subject_id);
            $section = $subject->section;

            $students = $section->students()->with(['grades' => function ($q) use ($subject) {
                $q->where('subject_id', $subject->id);
            }])->get();
        }

        return view('teachers.grades', compact('subjects', 'subject', 'section', 'students'));
    }


    public function encodeGrades(Subject $subject, Section $section)
    {
        // kunin students sa section na ito
        $enrollments = $section->enrollments()->with('student.profile')->get();

        $students = [];
        foreach ($enrollments as $enrollment) {
            $student = $enrollment->student;
            if (! $student) continue;

            // kunin existing grades ng bawat student
            $grades = Grade::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->pluck('grade', 'quarter')
                ->toArray();

            $students[$student->id] = [
                'lrn'   => $student->lrn ?? 'N/A',
                'name'  => ($student->profile->last_name ?? '') . ', ' . ($student->profile->first_name ?? ''),
                'grades'=> $grades,
            ];
        }

        return view('teachers.encode-grades', compact('subject', 'section', 'students'));
    }

    public function editGrades(Subject $subject)
    {
        $section = $subject->section;

        // Students under that section
        $students = $section->students()->with(['grades' => function ($q) use ($subject) {
            $q->where('subject_id', $subject->id);
        }])->get();

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

        return redirect()
            ->route('teachers.grades', ['subject_id' => $request->subject_id])
            ->with('success', 'Grades saved successfully!');
    }



    public function encode($subjectId, $sectionId)
    {
        $subject = Subject::with('section.enrollments.student.profile')
            ->findOrFail($subjectId);

        $students = [];
        foreach ($subject->section->enrollments as $enrollment) {
            $student = $enrollment->student;
            $grades = Grade::where('student_id', $student->id)
                        ->where('subject_id', $subjectId)
                        ->pluck('grade', 'quarter')
                        ->toArray();

            $students[$student->id] = [
                'lrn'    => $student->lrn,
                'name'   => $student->profile->last_name . ', ' . $student->profile->first_name,
                'grades' => $grades,
            ];
        }

        return view('teachers.encode-grades', compact('subject', 'students'));
    }

    // ---------------- REPORTS ----------------
    public function reports()
    {
        $gradeLevels = GradeLevel::all();
        $sections = Section::with('gradeLevel')->get();
        $schoolYears = ['2022-2023', '2023-2024', '2024-2025']; // pwede mo din gawin dynamic kung may table ka
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

        // Update email directly in users table
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

        return back()->with('success', 'Password changed successfully!');
    }
}
