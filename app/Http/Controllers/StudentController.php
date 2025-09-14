<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Announcement;
use App\Models\Grade;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $announcements = Announcement::with(['user', 'section'])
            ->where(function ($query) use ($user) {
                $query->whereNull('section_id')
                      ->orWhereIn('section_id', $user->enrollments->pluck('section_id'));
            })
            ->latest()
            ->take(5)
            ->get();

        $grades = Grade::with('subject')
            ->where('student_id', $user->id)
            ->get();

        return view('students.dashboard', compact('announcements', 'grades'));
    }

    public function announcements()
    {
        $user = Auth::user();
        $sectionIds = $user->enrollments->pluck('section_id')->toArray();

        $announcements = Announcement::with(['user', 'section'])
            ->whereNull('section_id')
            ->orWhereIn('section_id', $sectionIds)
            ->latest()
            ->get();

        return view('students.announcements', compact('announcements'));
    }

    public function assignments()
    {
        $user = Auth::user();
        $sectionIds = $user->enrollments->pluck('section_id')->toArray();

        if (empty($sectionIds)) {
            $assignments = collect(); 
        } else {
            $assignments = \App\Models\Assignment::with(['teacher.profile', 'section.gradeLevel', 'subject'])
                ->whereIn('section_id', $sectionIds)
                ->latest()
                ->get();
        }

        return view('students.assignments', compact('assignments'));
    }

    public function grades()
    {
        $user = Auth::user();

        $grades = Grade::with('subject')
            ->where('student_id', $user->id)
            ->get();

        return view('students.grades', compact('grades'));
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

        // Update email sa `users` table
        $student->update([
            'email' => $validated['email'],
        ]);

        // Siguraduhin may profile
        $profile = $student->profile;
        if (!$profile) {
            $profile = $student->profile()->create([
                'profile_picture' => 'images/default.png',
            ]);
        }

        // Upload profile picture kung meron
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $profile->profile_picture = $path;
        }

        // Update profile info
        $profile->first_name     = $validated['first_name'];
        $profile->middle_name    = $validated['middle_name'] ?? null;
        $profile->last_name      = $validated['last_name'];
        $profile->contact_number = $validated['contact_number'] ?? null;
        $profile->sex            = $validated['sex'] ?? null;
        $profile->birthdate      = $validated['birthdate'] ?? null;
        $profile->address        = $validated['address'] ?? null;
        $profile->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $student = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'new_password'     => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if (!\Hash::check($request->current_password, $student->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $student->update([
            'password' => \Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}