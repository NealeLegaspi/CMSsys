<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\Grade;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // 🔹 Get announcements (general + for student's section)
        $announcements = Announcement::with(['user', 'section'])
            ->whereNull('section_id')
            ->orWhereIn('section_id', $user->enrollments->pluck('section_id'))
            ->latest()
            ->take(5)
            ->get();

        // 🔹 Get student's grades
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
        $user = Auth::user();
        return view('students.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('students.settings')
            ->with('success', 'Settings updated successfully.');
    }
}
