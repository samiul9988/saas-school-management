<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\School\AcademicYearController;
use App\Http\Controllers\School\AttendanceController;
use App\Http\Controllers\School\DashboardController as SchoolDashboardController;
use App\Http\Controllers\School\ExamController;
use App\Http\Controllers\School\ExamSubjectController;
use App\Http\Controllers\School\ExpenseController;
use App\Http\Controllers\School\FeePaymentController;
use App\Http\Controllers\School\FeeTypeController;
use App\Http\Controllers\School\FinanceReportController;
use App\Http\Controllers\School\GradeController;
use App\Http\Controllers\School\IncomeController;
use App\Http\Controllers\School\MarkController;
use App\Http\Controllers\School\NoticeController;
use App\Http\Controllers\School\ResultController;
use App\Http\Controllers\School\RoutineController;
use App\Http\Controllers\School\SmsSettingController;
use App\Http\Controllers\School\StudentFeeController;
use App\Http\Controllers\School\StudentPortalAccountController;
use App\Http\Controllers\Parent\PortalController as ParentPortalController;
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

    if ($user->isParent()) {
        return redirect()->route('parent.dashboard');
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

        Route::get('/finance', FinanceReportController::class)->name('finance.index');
        Route::resource('fee-types', FeeTypeController::class)->except('show');
        Route::resource('student-fees', StudentFeeController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::get('/student-fees/{studentFee}/pay', [FeePaymentController::class, 'create'])->name('fee-payments.create');
        Route::post('/student-fees/{studentFee}/pay', [FeePaymentController::class, 'store'])->name('fee-payments.store');
        Route::get('/fee-payments/{feePayment}/receipt', [FeePaymentController::class, 'receipt'])->name('fee-payments.receipt');
        Route::resource('incomes', IncomeController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'destroy']);

        Route::resource('notices', NoticeController::class)->except('show');

        Route::get('/sms-settings', [SmsSettingController::class, 'edit'])->name('sms-settings.edit');
        Route::put('/sms-settings', [SmsSettingController::class, 'update'])->name('sms-settings.update');

        Route::post('/students/{student}/portal-account', [StudentPortalAccountController::class, 'store'])->name('students.portal-account.store');
        Route::delete('/students/{student}/portal-account', [StudentPortalAccountController::class, 'destroy'])->name('students.portal-account.destroy');
    });

Route::middleware(['auth', 'verified', 'parent_portal'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/attendance', [ParentPortalController::class, 'attendance'])->name('attendance');
        Route::get('/fees', [ParentPortalController::class, 'fees'])->name('fees');
        Route::get('/results', [ParentPortalController::class, 'results'])->name('results');
        Route::get('/results/{exam}/marksheet', [ParentPortalController::class, 'marksheet'])->name('results.marksheet');
        Route::get('/notices', [ParentPortalController::class, 'notices'])->name('notices');
    });

require __DIR__.'/auth.php';
