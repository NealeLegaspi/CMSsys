<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Role;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Setting;
use App\Models\GradeLevel;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\EnrollmentReportExport;
use App\Exports\GradingReportExport;
use App\Exports\PDF\EnrollmentReportPDF;
use App\Exports\PDF\GradingReportPDF;

class AdminController extends Controller
{
    /**
     * Dashboard
     */
    public function dashboard()
    {
        $userCount      = User::count();
        $teacherCount   = User::whereHas('role', fn($q) => $q->where('name', 'Teacher'))->count();
        $studentCount   = User::whereHas('role', fn($q) => $q->where('name', 'Student'))->count();
        $registrarCount = User::whereHas('role', fn($q) => $q->where('name', 'Registrar'))->count();
        $adminCount     = User::whereHas('role', fn($q) => $q->where('name', 'Admin'))->count();

        $logs = ActivityLog::with('user')->latest()->take(5)->get();

        $enrollmentTrends = Enrollment::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $roleDistribution = [
            'Administrators' => $adminCount,
            'Registrars'     => $registrarCount,
            'Teachers'       => $teacherCount,
            'Students'       => $studentCount,
        ];

        $announcements = Announcement::latest()->take(5)->get();

        return view('admins.dashboard', compact(
            'userCount','teacherCount','studentCount','registrarCount',
            'adminCount','logs','enrollmentTrends','roleDistribution', 'announcements'
        ));
    }

    /**
     * Announcements
     */
    public function announcements(Request $request)
    {
        $query = Announcement::with('user.profile')
            ->where(function($q) {
                $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
            })
            ->whereHas('user', function ($q) {
                $q->where('role_id', 1); 
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('target_type') && $request->target_type != 'All') {
            $query->where('target_type', $request->target_type);
        }

        $announcements = $query->latest()->paginate(10)->withQueryString();

        $teachers = User::where('role_id', 3)->with('profile')->get();
        $students = User::where('role_id', 4)->with('profile')->get();
        
        return view('admins.announcements', compact('announcements', 'teachers', 'students'));
    }



    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:150',
            'content'     => 'required|string',
            'target_type' => 'required|in:Global,Teacher,Student',
            'target_id'   => 'nullable|exists:users,id',
            'expires_at'  => 'nullable|date|after_or_equal:today',
        ]);

        Announcement::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'content'     => $request->content,
            'target_type' => $request->target_type,
            'target_id'   => $request->target_id,
            'expires_at'  => $request->expires_at,
        ]);

        $this->logActivity('Create Announcement', "Added {$request->target_type} announcement: {$request->title}");

        return back()->with('success', 'Announcement created successfully.');
    }

    public function updateAnnouncement(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:150',
            'content'     => 'required|string',
            'target_type' => 'required|in:Global,Teacher,Student',
            'target_id'   => 'nullable|exists:users,id',
            'expires_at'  => 'nullable|date|after_or_equal:today',
        ]);

        $announcement->update([
            'title'       => $request->title,
            'content'     => $request->content,
            'target_type' => $request->target_type,
            'target_id'   => $request->target_id,
            'expires_at'  => $request->expires_at,
        ]);

        $this->logActivity('Update Announcement', "Updated {$announcement->target_type} announcement: {$announcement->title}");

        return back()->with('success', 'Announcement updated successfully.');
    }

    public function destroyAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        $this->logActivity('Delete Announcement', "Deleted announcement: {$announcement->title}");

        return back()->with('success','Announcement deleted successfully.');
    }

    public function studentRecords(Request $request)
    {
        $query = Enrollment::with(['student.user.profile', 'section.gradeLevel', 'schoolYear']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user.profile', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%");
            })->orWhereHas('section', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        if ($request->filled('grade_level')) {
            $query->whereHas('section.gradeLevel', function ($q) use ($request) {
                $q->where('name', $request->grade_level);
            });
        }

        if ($request->filled('school_year')) {
            $query->whereHas('schoolYear', function ($q) use ($request) {
                $q->where('name', $request->school_year);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->paginate(10)->withQueryString();

        $gradeLevels = GradeLevel::pluck('name');
        $schoolYears = SchoolYear::pluck('name');

        return view('admins.student-records', compact('records', 'gradeLevels', 'schoolYears'));
    }

    public function exportStudentRecords(Request $request, $format)
    {
        $query = Enrollment::with(['student.user.profile', 'section.gradeLevel', 'schoolYear']);

        if ($request->filled('grade_level')) {
            $query->whereHas('section.gradeLevel', fn($q) => $q->where('name', $request->grade_level));
        }

        if ($request->filled('school_year')) {
            $query->whereHas('schoolYear', fn($q) => $q->where('name', $request->school_year));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->get();

        if (in_array($format, ['xlsx', 'csv'])) {
            $export = new EnrollmentReportExport($request->school_year, $request->status);
            $filename = "student_records.{$format}";
            return Excel::download($export, $filename);
        }

        if ($format === 'pdf') {
            $schoolName = Setting::where('key', 'school_name')->value('value') ?? 'Children’s Mindware School Inc.';
            $schoolAddress = Setting::where('key', 'school_address')->value('value') ?? 'Balagtas, Bulacan';

            $pdf = PDF::loadView('exports.student-records-pdf', [
                'records' => $records,
                'schoolName' => $schoolName,
                'schoolAddress' => $schoolAddress,
                'generatedAt' => now()->format('F d, Y h:i A'),
            ])->setPaper('a4', 'landscape');

            return $pdf->download('student_records.pdf');
        }

        return back()->with('error', 'Invalid export format.');
    }

    /**
     * User Management
     */
    public function users(Request $request)
    {
        $query = User::with(['role', 'profile']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admins.users', compact('users', 'roles'));
    }

    public function storeUser(Request $request)
    {
         $validated = $request->validate([
            'first_name'      => 'required|string|max:50',
            'middle_name'     => 'nullable|string|max:50',
            'last_name'       => 'required|string|max:50',
            'sex'             => 'nullable|in:Male,Female',
            'birthdate'       => 'nullable|date',
            'address'         => 'nullable|string|max:255',
            'contact_number'  => 'nullable|string|max:20',
            'email'           => 'required|email|unique:users,email',
            'password'        => [
                'required', 'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'
            ],
            'role_id'         => 'required|exists:roles,id',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'password.regex' => 'Password must contain at least 8 characters, including uppercase, lowercase, number, and special character.'
        ]);

        $studentRole = Role::where('name', 'Student')->first(); 
        $studentRoleId = $studentRole ? $studentRole->id : 0;

        DB::transaction(function () use ($validated, $request, $studentRoleId) {

        $user = User::create([
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role_id'  => $validated['role_id'],
            'status'   => 'active',
        ]);

        $path = null;
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
        }

        $user->profile()->create([
            'first_name'      => $validated['first_name'],
            'middle_name'     => $validated['middle_name'] ?? null,
            'last_name'       => $validated['last_name'],
            'sex'             => $validated['sex'] ?? null,
            'birthdate'       => $validated['birthdate'] ?? null,
            'address'         => $validated['address'] ?? null,
            'contact_number'  => $validated['contact_number'] ?? null,
            'profile_picture' => $path ?? 'images/default.png',
        ]);

        if ($user->role_id == $studentRoleId) {
            
            $studentNumber = date('Y') . str_pad($user->id, 5, '0', STR_PAD_LEFT);

            Student::create([
                'user_id'          => $user->id,
                'student_number'   => $studentNumber, 
            ]);
        }

        $this->logActivity('Create User', "Created user: {$user->email}");
        });

        return back()->with('success', 'User added successfully!');
    }


    public function updateUser(Request $request, $id)
    {
        $user = User::with('profile')->findOrFail($id);

        $request->validate([
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'role_id'         => 'required|exists:roles,id',
            'first_name'      => 'required|string|max:50',
            'middle_name'     => 'nullable|string|max:50',
            'last_name'       => 'required|string|max:50',
            'sex'             => 'nullable|in:Male,Female',
            'birthdate'       => 'nullable|date',
            'address'         => 'nullable|string|max:255',
            'contact_number'  => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $user->update([
            'email'   => $request->email,
            'role_id' => $request->role_id,
        ]);
            
        if ($request->hasFile('profile_picture')) {
            if ($user->profile && $user->profile->profile_picture && Storage::disk('public')->exists($user->profile->profile_picture)) {
                Storage::disk('public')->delete($user->profile->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profiles', 'public');
        } else {
            $path = $user->profile->profile_picture ?? 'images/default.png';
        }

        $user->profile()->updateOrCreate(
            ['user_id'=>$user->id],
            [
                'first_name'=>$request->first_name,
                'middle_name'=>$request->middle_name,
                'last_name'=>$request->last_name,
                'sex'=>$request->sex,
                'birthdate'=>$request->birthdate,
                'address'=>$request->address,
                'contact_number'=>$request->contact_number,
                'profile_picture'=>$path
            ]
        );

        $this->logActivity('Update User', "Updated user: {$user->email}");

        return back()->with('success', 'User updated successfully.');
    }

    public function toggleUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        $this->logActivity('Toggle User Status', "Changed status of {$user->email} to {$user->status}");

        return back()->with('success','User status updated.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $lastName = $user->profile->last_name ?? 'User';
        $firstName = $user->profile->first_name ?? 'X';

        $tempPassword = ucfirst($lastName) . ucfirst($firstName) . $user->id;

        $user->password = Hash::make($tempPassword);
        $user->save();

        $this->logActivity('Reset Password', "Reset password for {$user->email}");

        return back()->with('success', "Password has been reset to the default: {$tempPassword}");
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->profile && $user->profile->profile_picture) {
            if (Storage::disk('public')->exists($user->profile->profile_picture)) {
                Storage::disk('public')->delete($user->profile->profile_picture);
            }
        }

        $email = $user->email;
        $user->delete();

        $this->logActivity('Delete User', "Deleted user: {$email}");

        return back()->with('success', 'User deleted successfully.');
    }

    public function approveUser($id)
    {
        $user = User::with('student.enrollments')->findOrFail($id);

        if ($user->status !== 'pending') {
            return back()->withErrors(['error' => 'User is not pending approval.']);
        }

        $user->update(['status' => 'active']);

        if ($user->student && $user->student->enrollments) {
            foreach ($user->student->enrollments as $enrollment) {
                if ($enrollment->status === 'For Verification') {
                    $enrollment->update(['status' => 'enrolled']);
                }
            }
        }

        $this->logActivity('Approve User', "Approved user {$user->email} and verified enrollment(s).");

        return back()->with('success', 'User account approved and enrollment verified successfully.');
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->profile) {
            $user->profile->delete();
        }

        if ($user->student) {
            $user->student->delete();
        }

        $user->delete();

        return redirect()->back()->with('success', 'User rejected and removed successfully.');
    }



    /**
     * Import/Export Users
     */
    public function exportUsers()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new UsersImport, $request->file('file'));

        $this->logActivity('Import Users', "Imported user list via Excel/CSV");

        return back()->with('success', 'Users imported successfully.');
    }

    /**
     * Reports
     */
    public function reports(Request $request)
    {
        $schoolYears = SchoolYear::orderByDesc('start_date')->get();
        $gradeLevels = GradeLevel::all();
        $subjects    = Subject::all()->keyBy('id'); 

        $sy = $request->school_year_id ?? $schoolYears->first()?->id;
        $status = $request->status ?? 'all';

        $enrollments = Enrollment::with('section.gradeLevel','student.user.profile')
            ->when($sy, fn($q) => $q->where('school_year_id',$sy))
            ->when($status !== 'all', fn($q) => $q->where('status',$status))
            ->paginate(10);
            

        $enrollmentData = $enrollments
            ->groupBy(fn($e) => $e->section->gradeLevel->name ?? 'Unknown')
            ->map->count();

        $gradingData = \App\Models\Grade::selectRaw('subject_id, AVG(grade) as avg')
            ->groupBy('subject_id')
            ->pluck('avg', 'subject_id');

            
        $totalStudents    = User::where('role_id',4)->count();
        $totalTeachers    = User::where('role_id',3)->count();
        $totalEnrollments = $enrollments->count();
        $totalSections    = Section::count();
        $totalSubjects    = Subject::count();

        return view('admins.reports', compact(
            'schoolYears','gradeLevels','sy','status',
            'enrollmentData','gradingData','enrollments',
            'totalStudents','totalTeachers','totalEnrollments',
            'totalSections','totalSubjects','subjects' 
        ));
    }
    
    public function exportReport($type, $format, Request $request)
    {
        $sy = $request->school_year_id;
        $status = $request->status ?? 'all';
        $subjects = Subject::all()->keyBy('id');

        if (in_array($format, ['xlsx', 'csv'])) {
            if ($type === 'enrollment') {
                return Excel::download(new EnrollmentReportExport($sy, $status), "enrollment_report.$format");
            }

            if ($type === 'grades') {
                return Excel::download(new GradingReportExport($sy), "grading_report.$format");
            }
        }

        if ($format === 'pdf') {
            if ($type === 'enrollment') {
                $data = Enrollment::with(['section.gradeLevel', 'student.user.profile', 'schoolYear'])
                    ->when($sy, fn($q) => $q->where('school_year_id', $sy))
                    ->when($status !== 'all', fn($q) => $q->where('status', $status))
                    ->get();

                $pdf = PDF::loadView('reports.enrollment_pdf', compact('data', 'sy'))
                    ->setPaper('a4', 'portrait');

                return $pdf->download("enrollment_report.pdf");
            }

            if ($type === 'grades') {
                $data = \App\Models\Grade::selectRaw('grades.subject_id, AVG(grades.grade) as avg')
                    ->join('students', 'grades.student_id', '=', 'students.id')
                    ->join('enrollments', 'students.id', '=', 'enrollments.student_id')
                    ->where('enrollments.school_year_id', $sy)
                    ->groupBy('grades.subject_id')
                    ->pluck('avg', 'grades.subject_id');

                $pdf = Pdf::loadView('reports.grading_pdf', compact('data','sy','subjects'))
                    ->setPaper('a4','landscape');

                return $pdf->download("grading_report.pdf");
            }
        }

        return back()->with('error', 'Invalid export request.');
    }


    /**
     * School Year Management (Admin only)
     */
    public function schoolYears(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $schoolYears = SchoolYear::latest()
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->when($status, fn($q) => $q->where('status', $status))
            ->paginate(10);

        return view('admins.schoolyear', compact('schoolYears'));
    }

    public function storeSchoolYear(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'name'       => 'nullable|string|max:100|unique:school_years,name',
        ]);

        $name = $request->name ?? $request->start_date . ' - ' . $request->end_date;

        if (SchoolYear::where('name', $name)->exists()) {
            return back()->withErrors(['name' => 'School year already exists.']);
        }

        $status = $request->has('set_active') ? 'active' : 'closed';

        if ($status === 'active') {
            SchoolYear::where('status', 'active')->update(['status' => 'closed']);
        }

        SchoolYear::create([
            'name'       => $name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => $status,
        ]);

        $this->logActivity('Add School Year', "Added school year {$name}");

        return back()->with('success', 'School year added.');
    }

    public function activateSchoolYear($id)
    {
        $sy = SchoolYear::findOrFail($id);

        if ($sy->status === 'active') {
            return back()->with('info', "{$sy->name} is already active.");
        }

        SchoolYear::where('status', 'active')->update(['status' => 'closed']);

        $sy->update(['status' => 'active']);

        Enrollment::where('school_year_id', '!=', $sy->id)
            ->where('status', 'Enrolled')
            ->update(['status' => 'Inactive']);

        Enrollment::where('school_year_id', $sy->id)
            ->whereIn('status', ['Inactive', 'Pending'])
            ->update(['status' => 'Enrolled']);

        $this->logActivity(
            'Activate School Year',
            "Activated school year {$sy->name}, reactivated its enrollments, and inactivated previous years."
        );

        return back()->with(
            'success',
            "School year {$sy->name} is now active. Related enrollments have been updated."
        );
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

        if ($sy->status !== 'active') {
            return back()->with('error', 'Only the active school year can be closed.');
        }

        $sy->update(['status' => 'closed']);

        Enrollment::where('school_year_id', $sy->id)
            ->where('status', 'Enrolled')
            ->update(['status' => 'Inactive']);

        $this->logActivity('Close School Year', "Closed school year {$sy->name}");

        return back()->with('success', 'School year closed successfully.');
    }

    public function archivedSchoolYears(Request $request)
    {
        $query = SchoolYear::onlyTrashed()
            ->orderBy('deleted_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('start_date', 'like', "%{$search}%")
                ->orWhere('end_date', 'like', "%{$search}%");
            });
        }

        $schoolYears = $query->paginate(10)->withQueryString();

        return view('admins.schoolyear_archived', compact('schoolYears'));
    }

    public function archiveSchoolYear($id)
    {
        $sy = SchoolYear::findOrFail($id);

        if ($sy->status === 'active') {
            return back()->with('error', 'Cannot archive an active school year.');
        }

        $sy->delete();

        $this->logActivity('Archive School Year', "Archived school year {$sy->name}");

        return back()->with('success', 'School year archived successfully!');
    }

    public function restoreSchoolYear($id)
    {
        $sy = SchoolYear::onlyTrashed()->findOrFail($id);
        $sy->restore();

        $this->logActivity('Restore School Year', "Restored school year {$sy->name}");

        return back()->with('success', 'School year restored successfully!');
    }


    /**
     * Activity Logs
     */
    public function logs(Request $request)
    {
        $baseQuery = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $baseQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $baseQuery->where('action', 'like', '%' . $request->action . '%');
        }
        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $baseQuery->where('created_at', '>=', now()->subDays(30))
                        ->paginate(20)
                        ->appends($request->query());

        $users = User::orderBy('email')->get();

        return view('admins.logs', compact('logs', 'users'));
    }


    public function archiveLog($id)
    {
        $log = ActivityLog::findOrFail($id);
        $log->is_archived = true;
        $log->save();

        return back()->with('success', 'Log archived successfully!');
    }

    public function unarchiveLog($id)
    {
        $log = ActivityLog::findOrFail($id);
        $log->is_archived = false;
        $log->save();

        return back()->with('success', 'Log restored to active successfully!');
    }

    /**
     * System Settings
     */
    public function systemSettings()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admins.system', compact('settings'));
    }

    public function updateSystemSettings(Request $request)
    {
        $data = $request->only(['school_name','school_address','school_contact']);
        foreach ($data as $key=>$value) {
            Setting::updateOrCreate(['key'=>$key],['value'=>$value]);
        }

        $this->logActivity('Update Settings','Updated system settings');

        return back()->with('success','System settings updated.');
    }

    /**
     * Backup/Restore
     */
    public function backup()
    {
        $filename = 'backup_' . date('Ymd_His') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        Storage::makeDirectory('backups');

        exec("mysqldump -u root -p database_name > {$path}");

        $this->logActivity('Backup DB','System database backed up');

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:sql',
        ]);

        $path = $request->file('file')->getRealPath();
        exec("mysql -u root -p database_name < {$path}");

        $this->logActivity('Restore DB','System database restored from backup');

        return back()->with('success','Database restored successfully.');
    }

    /**
     * Settings (Profile & Password)
     */
    public function settings()
    {
        $admin = Auth::user();
        return view('admins.settings', compact('admin'));
    }

    public function updateSettings(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'first_name'      => 'required|string|max:100',
            'middle_name'     => 'nullable|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'required|email|unique:users,email,' . $admin->id,
            'contact_number'  => 'nullable|string|max:20',
            'sex'             => 'nullable|in:Male,Female',
            'birthdate'       => 'nullable|date|before:today',
            'address'         => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $admin->update(['email'=>$validated['email']]);

        $profile = $admin->profile ?? $admin->profile()->create();

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures','public');
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

        $this->logActivity('Update Profile',"Updated profile: {$admin->email}");

        return back()->with('success', 'Settings updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password'=>'Incorrect current password.']);
        }

        $admin->update(['password'=>Hash::make($request->new_password)]);

        $this->logActivity('Change Password',"Changed password for {$admin->email}");

        return back()->with('success','Password changed successfully!');
    }

    /**
     * Helper: log activity
     */
    private function logActivity(string $action, string $description)
    {
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'role'        => Auth::user()->role->name ?? 'Unknown',
            'action'      => $action,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->header('User-Agent'),
        ]);
    }

    public function exportEnrollment(Request $request, $format)
    {
        $export = new EnrollmentReportExport($request->school_year_id, $request->status);

        if ($format === 'csv') {
            return Excel::download($export, 'enrollment_report.csv', \Maatwebsite\Excel\Excel::CSV);
        }
        return Excel::download($export, 'enrollment_report.xlsx');
    }

    public function exportGrading(Request $request, $format)
    {
        $export = new GradingReportExport($request->school_year_id);

        if ($format === 'csv') {
            return Excel::download($export, 'grading_report.csv', \Maatwebsite\Excel\Excel::CSV);
        }
        return Excel::download($export, 'grading_report.xlsx');
    }
    
    public function exportEnrollmentPDF(Request $request)
    {
        return EnrollmentReportPDF::generate($request->school_year_id, $request->status);
    }

    public function exportGradingPDF(Request $request)
    {
        return GradingReportPDF::generate($request->school_year_id);
    }
}
