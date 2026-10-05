<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\School\AcademicYearController;
use App\Http\Controllers\School\AttendanceController;
use App\Http\Controllers\School\DashboardController as SchoolDashboardController;
use App\Http\Controllers\School\ExamController;
use App\Http\Controllers\School\ExamSubjectController;
use App\Http\Controllers\School\GradeController;
use App\Http\Controllers\School\MarkController;
use App\Http\Controllers\School\ResultController;
use App\Http\Controllers\School\RoutineController;
use App\Http\Controllers\School\SchoolClassController;
use App\Http\Controllers\School\SectionController;
use App\Http\Controllers\School\StudentController;
use App\Http\Controllers\School\SubjectController;
use App\Http\Controllers\School\TeacherController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\SchoolController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = request()->user();

    if ($user->isSuperAdmin()) {
        return redirect()->route('super-admin.dashboard');
    }

    if ($user->school_id) {
        return redirect()->route('school.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/dashboard', SuperAdminDashboardController::class)->name('dashboard');
        Route::resource('schools', SchoolController::class);
        Route::resource('plans', PlanController::class)->except('show');
    });

Route::middleware(['auth', 'verified', 'school_context'])
    ->prefix('school')
    ->name('school.')
    ->group(function () {
        Route::get('/dashboard', SchoolDashboardController::class)->name('dashboard');
        Route::resource('academic-years', AcademicYearController::class)->except('show')->parameters(['academic-years' => 'academicYear']);
        Route::resource('classes', SchoolClassController::class)->except('show')->parameters(['classes' => 'class']);
        Route::resource('sections', SectionController::class)->except('show');
        Route::resource('subjects', SubjectController::class)->except('show');
        Route::resource('teachers', TeacherController::class);
        Route::resource('students', StudentController::class);
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

        Route::resource('grades', GradeController::class)->except('show');

        Route::resource('exams', ExamController::class);
        Route::get('/exams/{exam}/subjects/create', [ExamSubjectController::class, 'create'])->name('exams.subjects.create');
        Route::post('/exams/{exam}/subjects', [ExamSubjectController::class, 'store'])->name('exams.subjects.store');
        Route::delete('/exams/{exam}/subjects/{examSubject}', [ExamSubjectController::class, 'destroy'])->name('exams.subjects.destroy');

        Route::get('/exam-subjects/{examSubject}/marks', [MarkController::class, 'edit'])->name('marks.edit');
        Route::put('/exam-subjects/{examSubject}/marks', [MarkController::class, 'update'])->name('marks.update');

        Route::get('/results', [ResultController::class, 'index'])->name('results.index');
        Route::get('/results/{exam}/marksheet/{student}', [ResultController::class, 'marksheet'])->name('results.marksheet');

        Route::get('/routine', [RoutineController::class, 'index'])->name('routine.index');
        Route::get('/routine/create', [RoutineController::class, 'create'])->name('routine.create');
        Route::post('/routine', [RoutineController::class, 'store'])->name('routine.store');
        Route::delete('/routine/{routine}', [RoutineController::class, 'destroy'])->name('routine.destroy');
    });

require __DIR__.'/auth.php';
