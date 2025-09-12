<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role->name;

        if ($role === 'Administrator') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'Teacher') {
            return redirect()->route('teachers.dashboard');
        } elseif ($role === 'Student') {
            return redirect()->route('students.dashboard');
        } elseif ($role === 'Registrar') {
            return redirect()->route('registrar.dashboard');
        }
    }

    return view('welcome'); 
});

// -------------------- AUTH ROUTES --------------------
require __DIR__.'/auth.php';

// -------------------- PROFILE ROUTES --------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -------------------- ADMIN ROUTES --------------------
Route::prefix('admin')->middleware(['auth', 'role:Administrator'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    // Add more admin routes here
});

// -------------------- REGISTRAR ROUTES --------------------
Route::prefix('registrar')->middleware(['auth', 'role:Registrar'])->group(function () {
    Route::get('/dashboard', [RegistrarController::class, 'dashboard'])->name('registrar.dashboard');
    // Add more registrar routes here
});

// -------------------- TEACHER ROUTES --------------------
Route::prefix('teacher')->middleware(['auth', 'role:Teacher'])->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('teachers.dashboard');
    Route::get('/announcements', [TeacherController::class, 'announcements'])->name('teachers.announcements');
    Route::post('/announcements', [TeacherController::class, 'storeAnnouncement'])->name('teachers.announcements.store');
    Route::put('/announcements/{announcement}', [TeacherController::class, 'updateAnnouncement'])->name('teachers.announcements.update');
    Route::delete('/announcements/{announcement}', [TeacherController::class, 'destroyAnnouncement'])->name('teachers.announcements.destroy');

    Route::get('/assignments', [TeacherController::class, 'assignments'])->name('teachers.assignments');
    Route::post('/assignments', [TeacherController::class, 'storeAssignment'])->name('teachers.assignments.store');
    Route::get('/assignments/{assignment}/edit', [TeacherController::class, 'editAssignment'])->name('teachers.assignments.edit');
    Route::put('/assignments/{assignment}', [TeacherController::class, 'updateAssignment'])->name('teachers.assignments.update');
    Route::delete('/assignments/{assignment}', [TeacherController::class, 'destroyAssignment'])->name('teachers.assignments.destroy');


    Route::get('/grades', [TeacherController::class, 'grades'])->name('teachers.grades');
    Route::get('/grades/{subject}/{section}/encode', [TeacherController::class, 'encodeGrades'])->name('teachers.grades.encode');
    Route::post('/grades', [TeacherController::class, 'storeGrades'])->name('teachers.storeGrades');
    Route::get('/grades/{subject}/edit', [TeacherController::class, 'editGrades'])->name('teachers.grades.edit');
    Route::put('/grades/{subject}', [TeacherController::class, 'updateGrades'])->name('teachers.grades.update');
    Route::delete('/grades/{subject}', [TeacherController::class, 'destroyGrades'])->name('teachers.grades.destroy');

    Route::get('/reports', [TeacherController::class, 'reports'])->name('teachers.reports');
    Route::post('/reports', [TeacherController::class, 'storeReport'])->name('teachers.reports.store');
    Route::get('/reports/{report}/edit', [TeacherController::class, 'editReport'])->name('teachers.reports.edit');
    Route::put('/reports/{report}', [TeacherController::class, 'updateReport'])->name('teachers.reports.update');
    Route::delete('/reports/{report}', [TeacherController::class, 'destroyReport'])->name('teachers.reports.destroy');
    Route::post('/reports/filter', [TeacherController::class, 'filterReports'])->name('teachers.filterReports');
    Route::get('/reports/{report}/download', [TeacherController::class, 'downloadReport'])->name('teachers.reports.download');
    Route::post('/reports/export', [TeacherController::class, 'exportReportsPDF'])->name('teachers.reports.export.pdf');

    Route::get('/settings', [TeacherController::class, 'settings'])->name('teachers.settings');
    Route::put('/settings', [TeacherController::class, 'updateSettings'])->name('teachers.updateSettings');
    Route::post('/settings/change-password', [TeacherController::class, 'changePassword'])->name('teachers.changePassword');

    Route::get('/classlist', [TeacherController::class, 'classList'])->name('teachers.classlist');
    Route::get('/classlist/{section}', [TeacherController::class, 'viewClassList'])->name('teachers.viewClasslist');
    Route::post('/classlist/{section}/add', [TeacherController::class, 'addStudentToClass'])->name('teachers.classlist.addStudent');
    Route::delete('/classlist/{section}/remove/{student}', [TeacherController::class, 'removeStudentFromClass'])->name('teachers.classlist.removeStudent');
    Route::get('/classlist/{section}/export', [TeacherController::class, 'exportClassList'])->name('teachers.classlist.export');
});

// -------------------- STUDENT ROUTES --------------------
Route::prefix('student')->middleware(['auth', 'role:Student'])->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('students.dashboard');
    Route::get('/announcements', [StudentController::class, 'announcements'])->name('students.announcements');
    Route::get('/assignments', [StudentController::class, 'assignments'])->name('students.assignments');
    Route::get('/grades', [StudentController::class, 'grades'])->name('students.grades');

    Route::get('/settings', [StudentController::class, 'settings'])->name('students.settings');
    Route::put('/settings/update', [StudentController::class, 'updateSettings'])->name('students.update-settings');
    Route::post('/settings/change-password', [StudentController::class, 'changePassword'])->name('students.change-password');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');