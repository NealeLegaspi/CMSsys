<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\GradeLevel;
use App\Models\UserProfile;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;

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
        $studentCount = User::where('role_id', 4)->count();
        $teacherCount = User::where('role_id', 3)->count();
        $sectionCount = Section::count();
        $schoolYear   = SchoolYear::where('status', 'active')->latest()->first();

        // Students per Section (for bar chart)
        $sections = Section::pluck('name');
        $totals   = $sections->map(fn($sec) =>
            Enrollment::whereHas('section', fn($q) => $q->where('name', $sec))->count()
        );

        // Gender distribution (for pie chart)
        $genderLabels = ['Male', 'Female'];
        $genderData   = [
            UserProfile::where('sex', 'Male')->count(),
            UserProfile::where('sex', 'Female')->count(),
        ];

        return view('registrars.dashboard', compact(
            'studentCount', 'teacherCount', 'sectionCount', 'schoolYear',
            'sections', 'totals', 'genderLabels', 'genderData'
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
                'password' => bcrypt('password123'), // TODO: Replace with reset system
                'role_id'  => 4,
            ]);

            $user->profile()->create([
                'first_name'  => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name'   => $request->last_name,
            ]);

            // Generate unique student number
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
        $request->validate(['file'=>'required|mimes:xlsx,csv']);
        Excel::import(new StudentsImport, $request->file('file'));
        $this->logActivity('Import Students','Imported student list');
        return back()->with('success','Students imported successfully.');
    }

    /**
     * Enrollment
     */
    public function enrollment()
    {
        $schoolYears = SchoolYear::all();
        $students    = Student::with('user.profile')->get();
        $sections    = Section::with('gradeLevel')->get();
        $enrollments = Enrollment::with(['student.profile', 'section', 'schoolYear'])
            ->latest()->paginate(10);

        return view('registrars.enrollment', compact('students', 'sections', 'schoolYears', 'enrollments'));
    }

    public function storeEnrollment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        // check active school year
        $activeSY = SchoolYear::where('status', 'active')->first();
        if (!$activeSY) {
            return back()->withErrors(['school_year' => 'No active school year found.']);
        }

        // prevent duplicate enrollment
        $exists = Enrollment::where('student_id', $request->student_id)
            ->where('school_year_id', $activeSY->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['student_id' => 'This student is already enrolled in the active school year.']);
        }

        // check section capacity (if applicable)
        $section = Section::findOrFail($request->section_id);
        if ($section->capacity) {
            $count = Enrollment::where('section_id', $section->id)
                ->where('school_year_id', $activeSY->id)
                ->count();

            if ($count >= $section->capacity) {
                return back()->withErrors(['section_id' => 'This section has reached maximum capacity.']);
            }
        }

        Enrollment::create([
            'student_id'     => $request->student_id,
            'section_id'     => $request->section_id,
            'school_year_id' => $activeSY->id,
            'status'         => 'enrolled',
        ]);

        $this->logActivity('Enroll Student', "Enrolled student {$request->student_id} in section {$request->section_id}");

        return back()->with('success', 'Student enrolled successfully!');
    }

    public function destroyEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        $this->logActivity('Delete Enrollment', "Deleted enrollment ID {$id}");

        return back()->with('success', 'Enrollment record deleted.');
    }

    /**
     * Sections
     */
    public function sections(Request $request)
    {
        $query = Section::with('gradeLevel', 'schoolYear');

        // 🔍 Search by section name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 🎓 Filter by grade level
        if ($request->filled('gradelevel_id')) {
            $query->where('gradelevel_id', $request->gradelevel_id);
        }

        // 🏫 Filter by school year
        if ($request->filled('school_year_id')) {
            $query->where('school_year_id', $request->school_year_id);
        }

        $sections    = $query->paginate(10)->withQueryString();
        $gradeLevels = GradeLevel::all();
        $schoolYears = SchoolYear::all();

        return view('registrars.sections', compact('sections', 'gradeLevels', 'schoolYears'));
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100|unique:sections,name',
            'gradelevel_id'  => 'required|exists:grade_levels,id',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        Section::create($request->only('name', 'gradelevel_id', 'school_year_id'));

        $this->logActivity('Add Section', "Added section {$request->name}");

        return back()->with('success', 'Section added.');
    }

    public function updateSection(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:100|unique:sections,name,' . $section->id,
            'gradelevel_id'  => 'required|exists:grade_levels,id',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        $section->update($request->only('name', 'gradelevel_id', 'school_year_id'));

        $this->logActivity('Update Section', "Updated section {$section->name}");

        return back()->with('success', 'Section updated.');
    }

    public function destroySection($id)
    {
        $section = Section::findOrFail($id);

        // 🚫 Prevent deleting sections that already have enrollments
        if ($section->enrollments()->exists()) {
            return back()->withErrors(['msg' => 'Cannot delete section with enrolled students.']);
        }

        $section->delete();

        $this->logActivity('Delete Section', "Deleted section {$section->name}");

        return back()->with('success', 'Section deleted.');
    }

    /**
     * Subjects
     */
    public function subjects()
    {
        $subjects    = Subject::with('gradeLevel')->paginate(10);
        $gradeLevels = GradeLevel::all();
        return view('registrars.subjects', compact('subjects', 'gradeLevels'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100|unique:subjects,name',
            'grade_level_id' => 'required|exists:grade_levels,id',
        ]);

        Subject::create($request->only('name', 'grade_level_id'));

        $this->logActivity('Add Subject', "Added subject {$request->name}");

        return back()->with('success', 'Subject added.');
    }

    public function updateSubject(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'name'           => ['required', 'string', 'max:100', Rule::unique('subjects', 'name')->ignore($subject->id)],
            'grade_level_id' => 'required|exists:grade_levels,id',
        ]);

        $subject->update($request->only('name', 'grade_level_id'));

        $this->logActivity('Update Subject', "Updated subject {$subject->name}");

        return back()->with('success', 'Subject updated.');
    }

    public function destroySubject($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        $this->logActivity('Delete Subject', "Deleted subject {$subject->name}");

        return back()->with('success', 'Subject deleted.');
    }

    /**
     * Teachers
     */
    public function teachers(Request $request)
    {
        $search = $request->input('search');

        $teachers = User::where('role_id', 3)
            ->with('profile')
            ->when($search, function ($query, $search) {
                $query->whereHas('profile', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                    ->orWhere('middle_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%");
                })->orWhere('email', 'like', "%$search%");
            })
            ->paginate(10);

        return view('registrars.teachers', compact('teachers'));
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

        $teacher->profile()->delete();
        $teacher->delete();

        $this->logActivity('Delete Teacher', "Deleted teacher {$teacherName}");

        return back()->with('success', 'Teacher deleted successfully.');
    }

    public function showTeacher($id)
    {
        $teacher = User::where('role_id', 3)->with('profile')->findOrFail($id);
        return response()->json($teacher);
    }

    /**
     * School Year
     */
    public function schoolYear(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $schoolYears = SchoolYear::latest()
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->when($status, fn($q) => $q->where('status', $status))
            ->paginate(10);

        return view('registrars.schoolyear', compact('schoolYears'));
    }

    public function storeSchoolYear(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'name'       => 'nullable|string|max:100|unique:school_years,name',
        ]);

        $name = $request->name ?? $request->start_date . ' - ' . $request->end_date;

        SchoolYear::create([
            'name'       => $name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => 'inactive', 
        ]);

        $this->logActivity('Add School Year', "Added school year {$name}");

        return back()->with('success', 'School year added.');
    }

    public function updateSchoolYear(Request $request, $id)
    {
        $sy = SchoolYear::findOrFail($id);

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $sy->update([
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'name'       => $request->name ?? $request->start_date . ' - ' . $request->end_date,
        ]);

        $this->logActivity('Update School Year', "Updated school year {$sy->name}");

        return back()->with('success', 'School year updated.');
    }

    public function destroySchoolYear($id)
    {
        $sy = SchoolYear::findOrFail($id);
        $syName = $sy->name;
        $sy->delete();

        $this->logActivity('Delete School Year', "Deleted school year {$syName}");

        return back()->with('success', 'School year deleted.');
    }


    public function closeSchoolYear($id)
    {
        $sy = SchoolYear::findOrFail($id);
        SchoolYear::where('status', 'active')->update(['status' => 'closed']);
        $sy->update(['status' => 'active']);

        $this->logActivity('Change Active School Year', "Changed active school year to {$sy->name}");

        return back()->with('success', 'Active school year updated.');
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
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $registrar->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $registrar->update(['password' => Hash::make($request->new_password)]);

        $this->logActivity('Change Password', "Changed password for {$registrar->email}");

        return back()->with('success', 'Password changed successfully!');
    }
}
