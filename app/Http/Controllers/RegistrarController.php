<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrarController extends Controller
{
    /**
     * Dashboard overview
     */
    public function dashboard()
    {
        $studentCount = User::where('role_id', 4)->count(); // role_id 4 = Student
        $teacherCount = User::where('role_id', 3)->count(); // role_id 3 = Teacher
        $sectionCount = Section::count();
        $schoolYear   = SchoolYear::latest()->first();

        // chart: students per section
        $sections = Section::pluck('name');
        $totals   = $sections->map(fn($sec) =>
            Enrollment::whereHas('section', fn($q) => $q->where('name', $sec))->count()
        );

        return view('registrars.dashboard', compact(
            'studentCount', 'teacherCount', 'sectionCount', 'schoolYear',
            'sections', 'totals'
        ));
    }

    /**
     * Student Records
     */
    public function students(Request $request)
    {
        $query = User::where('role_id', 4)->with(['profile', 'student.section']);

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
            $query->whereHas('student.section', function ($q) use ($request) {
                $q->where('id', $request->section_id);
            });
        }

        $students = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $sections = \App\Models\Section::all();

        return view('registrars.students', compact('students', 'sections'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'email'       => 'required|email|unique:users',
            'first_name'  => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name'   => 'required|string|max:50',
        ]);

        $user = User::create([
            'email'    => $request->email,
            'password' => bcrypt('password123'), 
            'role_id'  => 4, 
        ]);

        $user->profile()->create([
            'first_name'  => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'   => $request->last_name,
        ]);

        $year          = now()->format('Y');
        $random        = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $studentNumber = $year . $random;

        $user->student()->create([
            'student_number' => $studentNumber,
        ]);

        return back()->with('success', 'Student added successfully. LRN: ' . $studentNumber);
    }

    public function updateStudent(Request $request, $id)
    {
        $student = User::findOrFail($id);

        $request->validate([
            'first_name'  => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name'   => 'required|string|max:50',
            'email'       => 'required|email|unique:users,email,' . $student->id,
        ]);

        $student->update([
            'email' => $request->email,
        ]);

        $student->profile->update([
            'first_name'  => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'   => $request->last_name,
        ]);

        return back()->with('success', 'Student updated successfully.');
    }

    public function destroyStudent($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Student deleted.');
    }

    /**
     * Enrollment
     */
    public function enrollment()
    {
        $students    = Student::with('user.profile')->get();
        $sections    = Section::with('gradeLevel')->get();
        $enrollments = Enrollment::with(['student.user.profile', 'section', 'schoolYear'])
            ->latest()
            ->paginate(10);

        return view('registrars.enrollment', compact('students', 'sections', 'enrollments'));
    }

    public function storeEnrollment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $activeSY = SchoolYear::where('status', 'active')->first();
        if (!$activeSY) {
            return back()->withErrors(['school_year' => 'No active school year. Please set one first.']);
        }

        Enrollment::create([
            'student_id' => $request->student_id,
            'section_id' => $request->section_id,
            'school_year_id' => $activeSY->id,
            'status' => 'enrolled',
        ]);

        return back()->with('success', 'Student enrolled successfully!');
    }

    public function destroyEnrollment($id)
    {
        Enrollment::findOrFail($id)->delete();
        return back()->with('success', 'Enrollment deleted.');
    }

    /**
     * Sections
     */
    public function sections()
    {
        $sections = Section::with(['gradeLevel', 'schoolYear'])->paginate(10);
        $gradeLevels = \App\Models\GradeLevel::orderBy('name')->get();
        $schoolYears = SchoolYear::orderBy('start_date', 'desc')->get();

        return view('registrars.sections', compact('sections', 'gradeLevels', 'schoolYears'));
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100|unique:sections',
            'gradelevel_id'  => 'required|exists:grade_levels,id',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        Section::create([
            'name'           => $request->name,
            'gradelevel_id'  => $request->gradelevel_id,
            'school_year_id' => $request->school_year_id,
        ]);

        return back()->with('success', 'Section created.');
    }

    public function destroySection($id)
    {
        Section::findOrFail($id)->delete();
        return back()->with('success', 'Section deleted.');
    }

    /**
     * Subjects
     */
    public function subjects()
    {
        $subjects = Subject::with('gradeLevel')->paginate(10);
        $gradeLevels = \App\Models\GradeLevel::all();
        return view('registrars.subjects', compact('subjects', 'gradeLevels'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:subjects',
            'grade_level_id' => 'required|exists:grade_levels,id',
        ]);

        Subject::create([
            'name' => $request->name,
            'grade_level_id' => $request->grade_level_id,
        ]);

        return back()->with('success', 'Subject added.');
    }

    public function destroySubject($id)
    {
        Subject::findOrFail($id)->delete();
        return back()->with('success', 'Subject deleted.');
    }

    /**
     * Teachers
     */
    public function teachers()
    {
        $teachers = User::where('role_id', 3)->with('profile')->paginate(10);
        return view('registrars.teachers', compact('teachers'));
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'email'      => 'required|email|unique:users',
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
        ]);

        $user = User::create([
            'email'    => $request->email,
            'password' => bcrypt('password123'),
            'role_id'  => 3,
        ]);

        $user->profile()->create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
        ]);

        return back()->with('success', 'Teacher added successfully.');
    }

    public function destroyTeacher($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Teacher deleted.');
    }

    /**
     * School Year
     */
    public function schoolYear()
    {
        $schoolYears = SchoolYear::orderByDesc('start_date', 'desc')->paginate(10);
        return view('registrars.schoolyear', compact('schoolYears'));
    }

    public function storeSchoolYear(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        SchoolYear::where('status', 'active')->update(['status' => 'closed']);

        SchoolYear::create([
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => 'active',
        ]);

        return back()->with('success', 'School year created and set to active.');
    }

    public function closeSchoolYear($id)
    {
        $sy = SchoolYear::findOrFail($id);
        $sy->update(['status' => 'closed']);
        return back()->with('success', 'School year closed.');
    }

    /**
     * Reports
     */
    public function reports()
    {
        $students = User::where('role_id', 4)->count();
        $teachers = User::where('role_id', 3)->count();
        $sections = Section::count();
        $activeSY = SchoolYear::where('status', 'active')->count();

        return view('registrars.reports', compact('students', 'teachers', 'sections', 'activeSY'));
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
            'email'           => 'required|email|unique:users,email,' . $registrar->id,
            'contact_number'  => 'nullable|string|max:20',
            'sex'             => 'nullable|in:Male,Female',
            'birthdate'       => 'nullable|date|before:today',
            'address'         => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $registrar->update([
            'email' => $validated['email'],
        ]);

        $profile = $registrar->profile ?? $registrar->profile()->create();

        if ($request->hasFile('profile_picture')) {
            $path                   = $request->file('profile_picture')->store('profile_pictures', 'public');
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
        $registrar = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $registrar->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $registrar->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}
