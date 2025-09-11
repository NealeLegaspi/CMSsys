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
use Illuminate\Support\Facades\DB;

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
    public function announcements()
    {
        $teacher = Auth::user();

        $myAnnouncements = Announcement::where('user_id', $teacher->id)
            ->latest()
            ->paginate(5);

        return view('teachers.announcements', compact('teacher', 'myAnnouncements'));
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
            'teacher_id' => Auth::id(),
        ]);

        return redirect()->route('teachers.assignments')->with('success', 'Assignment created.');
    }

    public function updateAssignment(Request $request, Assignment $assignment) {

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
        $assignment->delete();
        return redirect()->route('teachers.assignments')->with('success', 'Assignment deleted.');
    }

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
                ->with('student')
                ->get()
                ->map(fn($enr) => $enr->student->profile);

            $studentsFemale = Enrollment::where('section_id', $sectionId)
                ->whereHas('student.profile', fn($q) => $q->where('sex','Female'))
                ->with('student')
                ->get()
                ->map(fn($enr) => $enr->student->profile);
        }

        // Subjects handled (sample join, depende sa setup mo ng subjects table)
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



    // ---------------- GRADES ----------------
    public function grades()
    {
        $teacher = Auth::user();

        // Kunin subjects na hawak ng teacher
        $subjects = Subject::whereHas('assignments', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->get();

        return view('teachers.grades', compact('subjects'));
    }

    public function storeGrades(Request $request)
    {
        $request->validate([
            'grades' => 'required|array',
        ]);

        foreach ($request->grades as $studentId => $quarters) {
            foreach ($quarters as $quarter => $gradeValue) {
                if (!empty($gradeValue)) {
                    Grade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'quarter'    => $quarter,
                        ],
                        [
                            'grade'      => $gradeValue,
                            'teacher_id' => Auth::id(),
                            'subject_id' => $request->subject_id,
                        ]
                    );
                }
            }
        }

        return redirect()->route('teachers.grades')->with('success', 'Grades saved successfully!');
    }

    // ---------------- REPORTS ----------------
    public function reports()
    {
        $sections = Section::all();
        return view('teachers.reports', compact('sections'));
    }

    public function filterReports(Request $request)
    {
        $students = Student::with('user.profile', 'section.gradeLevel')
            ->when($request->section_id, function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            })
            ->get();

        return view('teachers.reports', compact('students'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('teachers.settings', compact('user'));
    }
}
