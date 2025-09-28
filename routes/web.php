<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\Announcement;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role->name;

        if ($role === 'Admin') {
            return redirect()->route('admins.dashboard');
        } elseif ($role === 'Teacher') {
            return redirect()->route('teachers.dashboard');
        } elseif ($role === 'Student') {
            return redirect()->route('students.dashboard');
        } elseif ($role === 'Registrar') {
            return redirect()->route('registrars.dashboard');
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
Route::prefix('admin')->middleware(['auth', 'role:Admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admins.dashboard');

    //Announcements
    Route::get('/announcements', [AdminController::class, 'announcements'])->name('admins.announcements');
    Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('admins.announcements.store');
    Route::put('/announcements/{id}', [AdminController::class, 'updateAnnouncement'])->name('admins.announcements.update');
    Route::delete('/announcements/{id}', [AdminController::class, 'destroyAnnouncement'])->name('admins.announcements.destroy');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('admins.users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admins.users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admins.users.update');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUser'])->name('admins.users.toggle');
    Route::post('/users/{id}/reset', [AdminController::class, 'resetPassword'])->name('admins.users.reset');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('admins.users.destroy');

    // Activity Logs
    Route::get('/logs', [AdminController::class, 'logs'])->name('admins.logs');
    Route::patch('/logs/{id}/archive', [AdminController::class, 'archive'])->name('admins.logs.archive');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admins.reports');
    Route::get('/reports/export/{type}/{format}', [AdminController::class,'exportReport'])->name('admins.reports.export');

    Route::get('/schoolyear', [AdminController::class, 'schoolYears'])->name('admins.schoolyear');
    Route::post('/schoolyear', [AdminController::class, 'storeSchoolYear'])->name('admins.schoolyear.store');
    Route::put('/schoolyear/{id}', [AdminController::class, 'updateSchoolYear'])->name('admins.schoolyear.update');
    Route::delete('/schoolyear/{id}', [AdminController::class, 'destroySchoolYear'])->name('admins.schoolyear.destroy');
    Route::post('/schoolyear/{id}/close', [AdminController::class, 'closeSchoolYear'])->name('admins.schoolyear.close');
    Route::post('/schoolyears/{id}/activate', [AdminController::class, 'activateSchoolYear'])->name('admins.schoolyear.activate');

    Route::get('/subjects', [AdminController::class, 'subjects'])->name('admins.subjects');
    Route::post('/subjects', [AdminController::class, 'storeSubject'])->name('admins.subjects.store');
    Route::get('/subjects/{id}', [AdminController::class, 'showSubject'])->name('admins.subjects.show');
    Route::put('/subjects/{id}', [AdminController::class, 'updateSubject'])->name('admins.subjects.update');
    Route::delete('/subjects/{id}', [AdminController::class, 'destroySubject'])->name('admins.subjects.destroy');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('admins.settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('admins.updateSettings');
    Route::post('/settings/change-password', [AdminController::class, 'changePassword'])->name('admins.changePassword');

    Route::get('/reports/enrollment/{format}', [AdminController::class, 'exportEnrollment'])->name('admins.export.enrollment');
    Route::get('/reports/grading/{format}', [AdminController::class, 'exportGrading'])->name('admins.export.grading');
    Route::get('/reports/enrollment/pdf', [AdminController::class, 'exportEnrollmentPDF'])->name('admins.export.enrollment.pdf');
    Route::get('/reports/grading/pdf', [AdminController::class, 'exportGradingPDF'])->name('admins.export.grading.pdf');
});

// -------------------- REGISTRAR ROUTES --------------------
Route::prefix('registrar')->middleware(['auth', 'role:Registrar'])->group(function () {
    Route::get('/dashboard', [RegistrarController::class, 'dashboard'])->name('registrars.dashboard');

    Route::get('/students', [RegistrarController::class, 'students'])->name('registrars.students');
    Route::post('/students', [RegistrarController::class, 'storeStudent'])->name('registrars.students.store');
    Route::get('/students/{id}', [RegistrarController::class, 'showStudent'])->name('registrars.students.show');
    Route::put('/students/{id}', [RegistrarController::class, 'updateStudent'])->name('registrars.students.update');
    Route::delete('/students/{id}', [RegistrarController::class, 'destroyStudent'])->name('registrars.students.destroy');
    Route::post('/students/import', [RegistrarController::class, 'importStudents'])->name('registrars.students.import');
    Route::get('/students/export', [RegistrarController::class, 'exportStudents'])->name('registrars.students.export');

    Route::get('/teachers', [RegistrarController::class, 'teachers'])->name('registrars.teachers');
    Route::post('/teachers', [RegistrarController::class, 'storeTeacher'])->name('registrars.teachers.store');
    Route::get('/teachers/{id}', [RegistrarController::class, 'showTeacher'])->name('registrars.teachers.show');
    Route::put('/teachers/{id}', [RegistrarController::class, 'updateTeacher'])->name('registrars.teachers.update');
    Route::delete('/teachers/{id}', [RegistrarController::class, 'destroyTeacher'])->name('registrars.teachers.destroy');

    Route::get('/sections', [RegistrarController::class, 'sections'])->name('registrars.sections');
    Route::post('/sections', [RegistrarController::class, 'storeSection'])->name('registrars.sections.store');
    Route::get('/sections/{id}', [RegistrarController::class, 'showSection'])->name('registrars.sections.show');
    Route::put('/sections/{id}', [RegistrarController::class, 'updateSection'])->name('registrars.sections.update');
    Route::delete('/sections/{id}', [RegistrarController::class, 'destroySection'])->name('registrars.sections.destroy');

    Route::get('/enrollment', [RegistrarController::class, 'enrollment'])->name('registrars.enrollment');
    Route::post('/enrollment', [RegistrarController::class, 'storeEnrollment'])->name('registrars.enrollment.store');
    Route::put('/enrollment/{id}/update', [RegistrarController::class, 'updateEnrollment'])->name('registrars.enrollment.update');
    Route::delete('/enrollment/{id}', [RegistrarController::class, 'destroyEnrollment'])->name('registrars.enrollment.destroy');
    Route::post('/enrollments/import', [RegistrarController::class, 'importEnrollments'])->name('registrars.enrollments.import');
    Route::get('/enrollments/export', [RegistrarController::class, 'exportEnrollments'])->name('registrars.enrollments.export');
    Route::get('registrar/enrollment/export/csv', [RegistrarController::class, 'exportCsv'])->name('registrars.enrollment.export.csv');
    Route::get('registrar/enrollment/export/pdf', [RegistrarController::class, 'exportPdf'])->name('registrars.enrollment.export.pdf');

    Route::post('/registrar/sections/{section}/assign-subject', [RegistrarController::class, 'assignSubject'])->name('registrars.sections.assign');

    Route::get('/reports', [RegistrarController::class, 'reports'])->name('registrars.reports');
    Route::post('/reports', [RegistrarController::class, 'storeReport'])->name('registrars.reports.store');
    Route::get('/reports/{report}/edit', [RegistrarController::class, 'editReport'])->name('registrars.reports.edit');
    Route::put('/reports/{report}', [RegistrarController::class, 'updateReport'])->name('registrars.reports.update');
    Route::delete('/reports/{report}', [RegistrarController::class, 'destroyReport'])->name('registrars.reports.destroy');
    Route::post('/reports/filter', [RegistrarController::class, 'filterReports'])->name('registrars.filterReports');
    Route::get('/reports/{report}/download', [RegistrarController::class, 'downloadReport'])->name('registrars.reports.download');
    Route::post('/reports/export', [RegistrarController::class, 'exportReportsPDF'])->name('registrars.reports.export.pdf');
    Route::get('/reports/masterlist', [RegistrarController::class, 'masterlist'])->name('registrars.reports.masterlist');
    Route::get('/reports/enrollment-summary', [RegistrarController::class, 'enrollmentSummary'])->name('registrars.reports.enrollment.summary');
    Route::get('/reports/grade-logs', [RegistrarController::class, 'gradeLogs'])->name('registrars.reports.grade.logs');

    Route::get('/settings', [RegistrarController::class, 'settings'])->name('registrars.settings');
    Route::put('/settings', [RegistrarController::class, 'updateSettings'])->name('registrars.updateSettings');
    Route::post('/settings/change-password', [RegistrarController::class, 'changePassword'])->name('registrars.changePassword');
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
    Route::get('/classlist/export', [TeacherController::class, 'exportClassList'])->name('teachers.classlist.export');
});

// -------------------- STUDENT ROUTES --------------------
Route::prefix('student')->middleware(['auth', 'role:Student'])->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('students.dashboard');
    Route::get('/announcements', [StudentController::class, 'announcements'])->name('students.announcements');
    Route::get('/assignments', [StudentController::class, 'assignments'])->name('students.assignments');
        Route::get('/assignments/{id}', [StudentController::class, 'showAssignment'])->name('student.assignments.show');
    Route::get('/grades', [StudentController::class, 'grades'])->name('students.grades');

    Route::get('/settings', [StudentController::class, 'settings'])->name('students.settings');
    Route::put('/settings/update', [StudentController::class, 'updateSettings'])->name('students.updateSettings');
    Route::post('/settings/change-password', [StudentController::class, 'changePassword'])->name('students.changePassword');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');