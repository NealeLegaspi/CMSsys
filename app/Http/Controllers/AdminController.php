<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Dashboard Overview
     */
    public function dashboard()
    {
        // Example counts
        $userCount = \App\Models\User::count();
        $teacherCount = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'Teacher'))->count();
        $studentCount = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'Student'))->count();
        $registrarCount = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'Registrar'))->count();
        $adminCount = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'Admin'))->count();

        // Activity logs
        $logs = \App\Models\ActivityLog::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Enrollment Trends (line chart)
        $enrollmentTrends = \App\Models\Enrollment::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // User Role Distribution (pie chart)
        $roleDistribution = [
            'Admins'       => $adminCount,
            'Registrars'     => $registrarCount,
            'Teachers'       => $teacherCount,
            'Students'       => $studentCount,
        ];

        return view('admins.dashboard', compact(
            'userCount', 'teacherCount', 'studentCount', 'registrarCount', 'adminCount',
            'logs', 'enrollmentTrends', 'roleDistribution'
        ));
    }


    /**
     * User Management
     */
    public function users()
    {
        $users = User::with('role')->paginate(10);
        $roles = Role::all();
        return view('admins.users', compact('users', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|exists:roles,id',
        ]);

        $tempPassword = Str::random(8);

        User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'role_id'    => $request->role_id,
            'status'     => 'active',
            'password'   => Hash::make($tempPassword),
        ]);

        return back()->with('success', "User created. Temporary password: {$tempPassword}");
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role->name === 'Administrator' && User::where('role_id', $user->role_id)->count() == 1) {
            return back()->with('error', 'Cannot modify the last Administrator account.');
        }

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,'.$id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update($request->all());

        return back()->with('success', 'User updated successfully.');
    }

    public function deactivateUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->role->name === 'Administrator' && User::where('role_id', $user->role_id)->count() == 1) {
            return back()->with('error', 'Cannot deactivate the last Administrator account.');
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', 'User status updated.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $tempPassword = Str::random(8);
        $user->password = Hash::make($tempPassword);
        $user->save();

        return back()->with('success', "Password reset. New temporary password: {$tempPassword}");
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->role->name === 'Administrator' && User::where('role_id', $user->role_id)->count() == 1) {
            return back()->with('error', 'Cannot delete the last Administrator account.');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    /**
     * Activity Logs
     */
    public function logs()
    {
        $logs = \App\Models\ActivityLog::latest()->paginate(15);
        return view('admins.logs', compact('logs'));
    }

    /**
     * Reports
     */
    public function reports()
    {
        $students = User::where('role_id',4)->count();
        $teachers = User::where('role_id',3)->count();
        $registrars = User::where('role_id',2)->count();
        $admins = User::where('role_id',1)->count();
        $sections = Section::count();
        $subjects = Subject::count();
        $schoolYears = SchoolYear::count();

        return view('admins.reports', compact(
            'students','teachers','registrars','admins',
            'sections','subjects','schoolYears'
        ));
    }

    /**
     * Settings (profile & password)
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

        $admin->update([
            'email' => $validated['email'],
        ]);

        $profile = $admin->profile ?? $admin->profile()->create();

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
        $admin = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}
