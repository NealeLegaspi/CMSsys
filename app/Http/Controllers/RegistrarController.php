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
use App\Exports\EnrollmentsExport;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $activeSY = SchoolYear::where('status', 'active')->first();

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
    public function enrollment(Request $request)
    {
        $schoolYears = SchoolYear::all();
        $students    = Student::with('user.profile')->get();
        $sections    = Section::with('gradeLevel')->get();

        $query = Enrollment::with(['student.user.profile', 'section', 'schoolYear']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user.profile', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%");
            })->orWhereHas('student', function ($q) use ($search) {
                $q->where('student_number', 'like', "%$search%");
            })->orWhereHas('section', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        if ($request->filled('school_year_id')) {
            $query->where('school_year_id', $request->school_year_id);
        }

        $enrollments = $query->latest()->paginate(10);

        return view('registrars.enrollment', compact('students', 'sections', 'schoolYears', 'enrollments'));
    }

    public function storeEnrollment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $activeSY = SchoolYear::where('status', 'active')->first();
        if (!$activeSY) {
            return back()->withErrors(['school_year' => 'No active school year found.']);
        }

        $exists = Enrollment::where('student_id', $request->student_id)
            ->where('school_year_id', $activeSY->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['student_id' => 'This student is already enrolled in the active school year.']);
        }

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

    public function updateEnrollment(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

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

        $this->logActivity('Update Enrollment', "Updated enrollment ID {$id}");

        return back()->with('success', 'Enrollment updated successfully!');
    }


    public function destroyEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        $this->logActivity('Delete Enrollment', "Deleted enrollment ID {$id}");

        return back()->with('success', 'Enrollment record deleted.');
    }

        public function exportCsv()
    {
        return Excel::download(new EnrollmentsExport, 'enrollments.csv');
    }

    public function exportPdf()
    {
        $enrollments = Enrollment::with(['student.user.profile', 'section', 'schoolYear'])->get();

        $pdf = Pdf::loadView('exports.enrollment-pdf', compact('enrollments'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('enrollments.pdf');
    }
    /**
     * Sections
     */
    public function sections(Request $request)
    {
        $query = Section::with(['gradeLevel', 'schoolYear', 'adviser.profile']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('gradelevel_id')) {
            $query->where('gradelevel_id', $request->gradelevel_id);
        }

        if ($request->filled('school_year_id')) {
            $query->where('school_year_id', $request->school_year_id);
        }

        $sections    = $query->paginate(10)->withQueryString();
        $gradeLevels = GradeLevel::all();
        $schoolYears = SchoolYear::all();
        $teachers    = User::where('role_id', 3)->with('profile')->get();

        $subjects = Subject::with('gradeLevel')->orderBy('grade_level_id')->orderBy('name')->get();

        return view('registrars.sections', compact('sections', 'gradeLevels', 'schoolYears', 'teachers', 'subjects'));
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100|unique:sections,name',
            'gradelevel_id'  => 'required|exists:grade_levels,id',
            'school_year_id' => 'required|exists:school_years,id',
            'adviser_id'     => 'nullable|exists:users,id',
            'capacity'       => 'required|integer|min:10|max:100',
        ]);

        Section::create($request->only('name', 'gradelevel_id', 'school_year_id', 'adviser_id', 'capacity'));

        $this->logActivity('Add Section', "Added section {$request->name}");

        return back()->with('success', 'Section added successfully.');
    }

    public function updateSection(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:100|unique:sections,name,' . $section->id,
            'gradelevel_id'  => 'required|exists:grade_levels,id',
            'school_year_id' => 'required|exists:school_years,id',
            'adviser_id'     => 'nullable|exists:users,id',
            'capacity'       => 'required|integer|min:10|max:100',
        ]);

        $section->update($request->only('name', 'gradelevel_id', 'school_year_id', 'adviser_id', 'capacity'));

        $this->logActivity('Update Section', "Updated section {$section->name}");

        return back()->with('success', 'Section updated successfully.');
    }

    public function destroySection($id)
    {
        $section = Section::findOrFail($id);

        if ($section->enrollments()->exists()) {
            return back()->withErrors(['msg' => '❌ Cannot delete section with enrolled students.']);
        }

        $section->adviser_id = null;
        $section->save();

        $name = $section->name;
        $section->delete();

        $this->logActivity('Delete Section', "Deleted section {$name}");

        return back()->with('success', '✅ Section deleted successfully.');
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

    public function assignSubject(Request $request, Section $section)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $teacher = User::findOrFail($request->teacher_id);

        // extra safety: ensure selected user is actually a teacher role
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
    public function reports()
    {
        $students = User::where('role_id', 4)->count();
        $teachers = User::where('role_id', 3)->count();
        $sections = Section::count();
        $activeSY = SchoolYear::where('status', 'active')->first();

        $enrollments = Enrollment::with('schoolYear')->get();

        return view('registrars.reports', compact(
            'students',
            'teachers',
            'sections',
            'activeSY',
            'enrollments'
        ));
    }

    public function masterlist()
    {
        $students = User::with('profile')
            ->where('role_id', 4)
            ->orderBy('id','asc')
            ->get();

        $pdf = PDF::loadView('reports.masterlist', compact('students'));
        return $pdf->download('masterlist.pdf');
    }

    public function enrollmentSummary()
    {
        $enrollments = Enrollment::with(['student.user.profile','section','schoolYear'])
            ->orderBy('school_year_id','desc')
            ->get();

        $pdf = PDF::loadView('reports.enrollment-summary', compact('enrollments'));
        return $pdf->download('enrollment-summary.pdf');
    }
    public function gradeLogs()
    {
        $logs = ActivityLog::where('action','like','%Grade%')
            ->orderBy('created_at','desc')
            ->get();

        $pdf = PDF::loadView('reports.grade-logs', compact('logs'));
        return $pdf->download('grade-validation-logs.pdf');
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
