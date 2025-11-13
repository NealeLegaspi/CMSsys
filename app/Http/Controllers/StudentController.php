<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Announcement;
use App\Models\Grade;
use App\Models\SubjectAssignment;
use App\Models\Assignment;
use App\Models\SchoolYear;
use App\Helpers\SystemHelper;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $activeSY = SchoolYear::where('status', 'active')->first();

        if (!$activeSY) {
            return view('students.dashboard', [
                'noActiveSY' => true,
                'announcements' => collect(),
                'grades' => collect(),
                'activeQuarter' => null,
            ]);
        }

        $activeQuarter = SystemHelper::getActiveQuarter();

        $sectionIds = $user->student?->enrollments?->pluck('section_id')->filter()->toArray() ?? [];

        $announcements = Announcement::with(['user.profile', 'section'])
            ->where(function ($q) use ($sectionIds) {
                $q->whereIn('target_type', ['Global'])
                ->orWhere(function ($sub) use ($sectionIds) {
                    $sub->where('target_type', 'Student')
                        ->whereIn('section_id', $sectionIds);
                });
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->take(5)
            ->get();

        $grades = Grade::with('subject')
            ->where('student_id', $user->student?->id)
            ->where('quarter', $activeQuarter)
            ->whereHas('subject.subjectAssignments', function ($q) {
                $q->where('grade_status', 'approved');
            })
            ->get();

        return view('students.dashboard', compact('announcements', 'grades', 'activeQuarter', 'activeSY'));
    }

    public function announcements()
    {
        $user = Auth::user();
        $activeSY = SchoolYear::where('status', 'active')->first();

        if (!$activeSY) {
            return view('students.announcements', [
                'announcements' => collect(),
                'noActiveSY' => true,
            ]);
        }

        $sectionIds = $user->student?->enrollments?->pluck('section_id')->filter()->toArray() ?? [];

        $announcements = Announcement::with(['user.profile', 'section'])
            ->where(function ($q) use ($sectionIds) {
                $q->whereIn('target_type', ['Global'])
                ->orWhere(function ($sub) use ($sectionIds) {
                    $sub->where('target_type', 'Student')
                        ->whereIn('section_id', $sectionIds);
                });
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->get();

        return view('students.announcements', compact('announcements', 'activeSY'));
    }


    public function grades()
    {
        $user = Auth::user();
        $activeSY = SchoolYear::where('status', 'active')->first();

        if (!$activeSY) {
            return view('students.grades', [
                'grades' => collect(),
                'noActiveSY' => true,
            ]);
        }

        $grades = Grade::with('subject')
            ->where('student_id', $user->student?->id)
            ->whereHas('subject.subjectAssignments', function ($q) {
                $q->where('grade_status', 'approved');
            })
            ->get()
            ->groupBy('subject.name');

        return view('students.grades', compact('grades', 'activeSY'));
    }



    public function settings()
    {
        $student = Auth::user();
        return view('students.settings', compact('student'));
    }

    public function updateSettings(Request $request)
    {
        $student = Auth::user();

        $validated = $request->validate([
            'first_name'      => ['required', 'string', 'max:100'],
            'middle_name'     => ['nullable', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'email'           => ['required', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'contact_number'  => ['nullable', 'string', 'max:20'],
            'sex'             => ['nullable', 'in:Male,Female'],
            'birthdate'       => ['nullable', 'date', 'before:today'],
            'address'         => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        $student->update([
            'email' => $validated['email'],
        ]);

        $profile = $student->profile;
        if (!$profile) {
            $profile = $student->profile()->create([
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

        $this->logActivity('Update Profile', "Updated profile for {$student->email}");

        return back()->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $student = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (!\Hash::check($request->current_password, $student->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $student->update([
            'password' => \Hash::make($request->new_password),
        ]);

        $this->logActivity('Change Password', "Changed password for {$student->email}");

        return back()->with('success', 'Password updated successfully!');
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