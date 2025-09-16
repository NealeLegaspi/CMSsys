<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Role;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Storage;

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
        $adminCount     = User::whereHas('role', fn($q) => $q->where('name', 'Administrator'))->count();

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
    public function announcements()
    {
        $announcements = Announcement::with('user')->latest()->paginate(10);
        return view('admins.announcements', compact('announcements'));
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:150',
            'content'    => 'required|string',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        Announcement::create([
            'user_id'    => Auth::id(),
            'title'      => $request->title,
            'content'    => $request->content,
            'section_id' => $request->section_id,
        ]);

        $this->logActivity('Create Announcement', "Added announcement: {$request->title}");

        return back()->with('success','Announcement created successfully.');
    }

    public function destroyAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        $title = $announcement->title;
        $announcement->delete();

        $this->logActivity('Delete Announcement', "Deleted announcement: {$title}");

        return back()->with('success','Announcement deleted successfully.');
    }

    /**
     * User Management
     */
    public function users()
    {
        $users = User::with('role', 'profile')->paginate(10);
        $roles = Role::all();
        return view('admins.users', compact('users', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'email'      => 'required|email|unique:users,email',
            'role_id'    => 'required|exists:roles,id',
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
        ]);

        $tempPassword = Str::random(8);

        $user = User::create([
            'email'    => $request->email,
            'role_id'  => $request->role_id,
            'password' => Hash::make($tempPassword),
            'status'   => 'active',
        ]);

        $user->profile()->create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
        ]);

        $this->logActivity('Create User', "Added user: {$user->email}");

        return back()->with('success', "User created successfully. Temporary Password: {$tempPassword}");
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role_id'    => 'required|exists:roles,id',
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
        ]);

        $user->update([
            'email'   => $request->email,
            'role_id' => $request->role_id,
        ]);

        $profile = $user->profile ?? $user->profile()->create();
        $profile->first_name = $request->first_name;
        $profile->last_name  = $request->last_name;
        $profile->save();

        $this->logActivity('Update User', "Updated user: {$user->email}");

        return back()->with('success', 'User updated successfully.');
    }

    public function deactivateUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'inactive']);

        $this->logActivity('Deactivate User', "Deactivated user: {$user->email}");

        return back()->with('success', 'User deactivated successfully.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $tempPassword = Str::random(8);
        $user->update(['password' => Hash::make($tempPassword)]);

        $this->logActivity('Reset Password', "Reset password for user: {$user->email}");

        return back()->with('success', "Password reset successfully. Temporary Password: {$tempPassword}");
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $email = $user->email;
        $user->delete();

        $this->logActivity('Delete User', "Deleted user: {$email}");

        return back()->with('success', 'User deleted successfully.');
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
    public function reports()
    {
        $students    = User::where('role_id', 4)->count();
        $teachers    = User::where('role_id', 3)->count();
        $registrars  = User::where('role_id', 2)->count();
        $admins      = User::where('role_id', 1)->count();
        $sections    = Section::count();
        $subjects    = Subject::count();
        $schoolYears = SchoolYear::count();

        return view('admins.reports', compact(
            'students','teachers','registrars','admins','sections','subjects','schoolYears'
        ));
    }

    /**
     * Activity Logs
     */
    public function logs(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);
        $users = User::orderBy('email')->get();

        return view('admins.logs', compact('logs', 'users'));
    }

    /**
     * System Settings
     */
    public function systemSettings()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admins.system_settings', compact('settings'));
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
            'new_password'     => 'required|min:8|confirmed',
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
}
