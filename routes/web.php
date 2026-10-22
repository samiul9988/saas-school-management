<?php

use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\Parent\PortalController as ParentPortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\School\AcademicYearController;
use App\Http\Controllers\School\AccountController;
use App\Http\Controllers\School\AccountingReportController;
use App\Http\Controllers\School\AdmitInstructionController;
use App\Http\Controllers\School\AttendanceController;
use App\Http\Controllers\School\AttendanceReportController;
use App\Http\Controllers\School\AttendanceSettingsController;
use App\Http\Controllers\School\AttendanceSmsController;
use App\Http\Controllers\School\BirthdaySmsController;
use App\Http\Controllers\School\BookCategoryController;
use App\Http\Controllers\School\BookController;
use App\Http\Controllers\School\BookIssueController;
use App\Http\Controllers\School\ClassConfigurationController;
use App\Http\Controllers\School\DashboardController as SchoolDashboardController;
use App\Http\Controllers\School\DesignationController;
use App\Http\Controllers\School\DueFeeSmsController;
use App\Http\Controllers\School\DynamicSmsController;
use App\Http\Controllers\School\ExamController;
use App\Http\Controllers\School\ExamSettingsController;
use App\Http\Controllers\School\ExamShortCodeController;
use App\Http\Controllers\School\ExamSubjectController;
use App\Http\Controllers\School\ExamSubjectSearchController;
use App\Http\Controllers\School\ExpenseController;
use App\Http\Controllers\School\FeePaymentController;
use App\Http\Controllers\School\FeeTypeController;
use App\Http\Controllers\School\FinanceReportController;
use App\Http\Controllers\School\FourthSubjectController;
use App\Http\Controllers\School\GradeController;
use App\Http\Controllers\School\GrandFinalController;
use App\Http\Controllers\School\GroupConfigurationController;
use App\Http\Controllers\School\GuardianSmsController;
use App\Http\Controllers\School\HrIdCardController;
use App\Http\Controllers\School\HrSmsController;
use App\Http\Controllers\School\IdCardController;
use App\Http\Controllers\School\IncomeController;
use App\Http\Controllers\School\InitialSetupController;
use App\Http\Controllers\School\InventoryCategoryController;
use App\Http\Controllers\School\InventoryItemController;
use App\Http\Controllers\School\InventoryTransactionController;
use App\Http\Controllers\School\JournalEntryController;
use App\Http\Controllers\School\MarkConfigurationController;
use App\Http\Controllers\School\MarkController;
use App\Http\Controllers\School\MarkInputController;
use App\Http\Controllers\School\MarkSheetController;
use App\Http\Controllers\School\MarksheetTemplateController;
use App\Http\Controllers\School\NoticeController;
use App\Http\Controllers\School\PayrollController;
use App\Http\Controllers\School\RemarksTemplateController;
use App\Http\Controllers\School\ReportController;
use App\Http\Controllers\School\ResultController;
use App\Http\Controllers\School\ResultProcessController;
use App\Http\Controllers\School\ResultSmsController;
use App\Http\Controllers\School\RouteStopController;
use App\Http\Controllers\School\RoutineController;
use App\Http\Controllers\School\SalaryStructureController;
use App\Http\Controllers\School\SchoolClassController;
use App\Http\Controllers\School\SchoolProfileController;
use App\Http\Controllers\School\SchoolUserController;
use App\Http\Controllers\School\SeatPlanController;
use App\Http\Controllers\School\SectionController;
use App\Http\Controllers\School\SignatureController;
use App\Http\Controllers\School\SmsCreditOrderController;
use App\Http\Controllers\School\SmsReportController;
use App\Http\Controllers\School\SmsTemplateController;
use App\Http\Controllers\School\SmsWalletController;
use App\Http\Controllers\School\StaffAttendanceController;
use App\Http\Controllers\School\StudentAttendanceController;
use App\Http\Controllers\School\StudentController;
use App\Http\Controllers\School\StudentFeeController;
use App\Http\Controllers\School\StudentImportController;
use App\Http\Controllers\School\StudentPortalAccountController;
use App\Http\Controllers\School\StudentReportController;
use App\Http\Controllers\School\StudentTransportController;
use App\Http\Controllers\School\StudentUpdateController;
use App\Http\Controllers\School\SubjectComponentController;
use App\Http\Controllers\School\SubjectConfigurationController;
use App\Http\Controllers\School\SubjectController;
use App\Http\Controllers\School\TabulationSheetController;
use App\Http\Controllers\School\TeacherController;
use App\Http\Controllers\School\TestimonialController;
use App\Http\Controllers\School\TopSheetController;
use App\Http\Controllers\School\TransferCertificateController;
use App\Http\Controllers\School\TransportRouteController;
use App\Http\Controllers\School\VehicleController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\SchoolController;
use App\Http\Controllers\SuperAdmin\SchoolSmsCreditOrderController;
use App\Http\Controllers\SuperAdmin\SmsCreditPurchaseController;
use App\Http\Controllers\SuperAdmin\SmsGatewaySettingController;
use App\Http\Controllers\SuperAdmin\SmsSchoolOverviewController;
use App\Http\Controllers\SuperAdmin\SmsUsageReportController;
use App\Http\Middleware\AllowActivationAccess;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ------------------------------------ Site Maintenance (login-free with a key) ------------------------------------
Route::middleware(AllowActivationAccess::class)->prefix('dashboard/activation')->group(function () {
    Route::get('/', [MaintenanceController::class, 'show'])->name('maintenance.show');
    Route::post('/toggle', [MaintenanceController::class, 'toggle'])->name('maintenance.toggle');
    Route::get('/preview', [MaintenanceController::class, 'preview'])->name('maintenance.preview');
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
    Route::post('/profile/school-logo', [ProfileController::class, 'updateLogo'])->name('profile.school-logo.update');
});

Route::middleware(['auth', 'verified', 'super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/dashboard', SuperAdminDashboardController::class)->name('dashboard');
        Route::resource('schools', SchoolController::class);
        Route::resource('plans', PlanController::class)->except('show');

        Route::get('/sms-gateway', [SmsGatewaySettingController::class, 'edit'])->name('sms-gateway.edit');
        Route::put('/sms-gateway', [SmsGatewaySettingController::class, 'update'])->name('sms-gateway.update');
        Route::post('/sms-gateway/sync-balance', [SmsGatewaySettingController::class, 'syncBalance'])->name('sms-gateway.sync-balance');

        Route::get('/sms-credit-purchases', [SmsCreditPurchaseController::class, 'index'])->name('sms-credit-purchases.index');
        Route::get('/sms-credit-purchases/create', [SmsCreditPurchaseController::class, 'create'])->name('sms-credit-purchases.create');
        Route::post('/sms-credit-purchases', [SmsCreditPurchaseController::class, 'store'])->name('sms-credit-purchases.store');

        Route::get('/sms-schools', [SmsSchoolOverviewController::class, 'index'])->name('sms-schools.index');
        Route::get('/sms-schools/{school}', [SmsSchoolOverviewController::class, 'show'])->name('sms-schools.show');

        Route::get('/sms-credit-orders', [SchoolSmsCreditOrderController::class, 'index'])->name('sms-credit-orders.index');
        Route::post('/sms-credit-orders/{order}/approve', [SchoolSmsCreditOrderController::class, 'approve'])->name('sms-credit-orders.approve');
        Route::post('/sms-credit-orders/{order}/reject', [SchoolSmsCreditOrderController::class, 'reject'])->name('sms-credit-orders.reject');

        Route::get('/sms-usage-report', [SmsUsageReportController::class, 'index'])->name('sms-usage-report.index');
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
        Route::get('/teachers/hr-list', [TeacherController::class, 'hrList'])->name('teachers.hr-list');
        Route::get('/teachers/update/basic-info', [TeacherController::class, 'hrUpdate'])->name('teachers.hr-update');
        Route::get('/teachers/update/active-status', [TeacherController::class, 'enableDisable'])->name('teachers.enable-disable');
        Route::post('/teachers/update/active-status', [TeacherController::class, 'enableDisableStore'])->name('teachers.enable-disable.store');
        Route::get('/designation', [DesignationController::class, 'index'])->name('designations.index');
        Route::post('/designation', [DesignationController::class, 'store'])->name('designations.store');
        Route::put('/designation/{designation}', [DesignationController::class, 'update'])->name('designations.update');
        Route::delete('/designation/{designation}', [DesignationController::class, 'destroy'])->name('designations.destroy');
        Route::get('/teachers/id-card', [HrIdCardController::class, 'index'])->name('teachers.id-card.index');
        Route::get('/teachers/id-card/single', [HrIdCardController::class, 'single'])->name('teachers.id-card.single');
        Route::post('/teachers/id-card/template', [HrIdCardController::class, 'selectTemplate'])->name('teachers.id-card.select-template');
        Route::get('/teachers/id-card/preview/{template}', [HrIdCardController::class, 'preview'])->whereNumber('template')->name('teachers.id-card.preview');
        Route::get('/teachers/id-card/{teacher}/download', [HrIdCardController::class, 'download'])->name('teachers.id-card.download');
        Route::resource('teachers', TeacherController::class);
        Route::get('/students/import', [StudentImportController::class, 'create'])->name('students.import.create');
        Route::get('/students/import/{mode}/sample', [StudentImportController::class, 'sample'])->whereIn('mode', StudentImportController::MODES)->name('students.import.sample');
        Route::post('/students/import/{mode}/preview', [StudentImportController::class, 'preview'])->whereIn('mode', StudentImportController::MODES)->name('students.import.preview');
        Route::post('/students/import/{mode}/store', [StudentImportController::class, 'store'])->whereIn('mode', StudentImportController::MODES)->name('students.import.store');
        Route::post('/students/import/{mode}/cancel', [StudentImportController::class, 'cancel'])->whereIn('mode', StudentImportController::MODES)->name('students.import.cancel');
        Route::get('/students/update/basic', [StudentUpdateController::class, 'basic'])->name('students.update-basic');
        Route::post('/students/update/basic', [StudentUpdateController::class, 'basicUpdate'])->name('students.update-basic.store');
        Route::get('/students/update/photo', [StudentUpdateController::class, 'photo'])->name('students.update-photo');
        Route::post('/students/update/photo', [StudentUpdateController::class, 'photoUpdate'])->name('students.update-photo.store');
        Route::get('/students/update/academic-info', [StudentUpdateController::class, 'academic'])->name('students.update-academic');
        Route::post('/students/update/academic-info', [StudentUpdateController::class, 'academicUpdate'])->name('students.update-academic.store');
        Route::get('/students/update/migration-without-merit', [StudentUpdateController::class, 'migrationWithoutMerit'])->name('students.migration-without-merit');
        Route::post('/students/update/migration-without-merit', [StudentUpdateController::class, 'migrationWithoutMeritStore'])->name('students.migration-without-merit.store');
        Route::get('/students/update/migration-with-merit', [StudentUpdateController::class, 'migrationWithMerit'])->name('students.migration-with-merit');
        Route::post('/students/update/migration-with-merit', [StudentUpdateController::class, 'migrationWithMeritStore'])->name('students.migration-with-merit.store');
        Route::get('/students/update/enable-disable', [StudentUpdateController::class, 'enableDisable'])->name('students.enable-disable');
        Route::post('/students/update/enable-disable', [StudentUpdateController::class, 'enableDisableStore'])->name('students.enable-disable.store');

        Route::get('/students/report/all-student', [StudentReportController::class, 'allStudent'])->name('students.report.all');
        Route::get('/students/report/section-wise-summary', [StudentReportController::class, 'sectionWiseSummary'])->name('students.report.section-summary');
        Route::get('/students/report/section-wise', [StudentReportController::class, 'sectionWise'])->name('students.report.section-wise');
        Route::get('/students/report/section-wise/pdf/{mode}', [StudentReportController::class, 'sectionWisePdf'])->whereIn('mode', ['with-image', 'without-image'])->name('students.report.section-wise.pdf');
        Route::get('/students/report/section-wise/excel', [StudentReportController::class, 'sectionWiseExcel'])->name('students.report.section-wise.excel');
        Route::get('/students/report/class-wise', [StudentReportController::class, 'classWise'])->name('students.report.class-wise');
        Route::get('/students/report/class-wise/pdf', [StudentReportController::class, 'classWisePdf'])->name('students.report.class-wise.pdf');
        Route::get('/students/report/class-wise/excel', [StudentReportController::class, 'classWiseExcel'])->name('students.report.class-wise.excel');
        Route::get('/students/report/group-wise', [StudentReportController::class, 'groupWise'])->name('students.report.group-wise');
        Route::get('/students/report/group-wise/pdf', [StudentReportController::class, 'groupWisePdf'])->name('students.report.group-wise.pdf');
        Route::get('/students/report/group-wise/excel', [StudentReportController::class, 'groupWiseExcel'])->name('students.report.group-wise.excel');
        Route::get('/students/report/gender-wise', [StudentReportController::class, 'genderWise'])->name('students.report.gender-wise');
        Route::get('/students/report/gender-wise/pdf', [StudentReportController::class, 'genderWisePdf'])->name('students.report.gender-wise.pdf');
        Route::get('/students/report/religion-wise', [StudentReportController::class, 'religionWise'])->name('students.report.religion-wise');
        Route::get('/students/report/religion-wise/pdf', [StudentReportController::class, 'religionWisePdf'])->name('students.report.religion-wise.pdf');

        Route::get('/students/id-card', [IdCardController::class, 'index'])->name('students.id-card.index');
        Route::post('/students/id-card/template', [IdCardController::class, 'selectTemplate'])->name('students.id-card.select-template');
        Route::get('/students/id-card/preview/{template}', [IdCardController::class, 'preview'])->whereNumber('template')->name('students.id-card.preview');
        Route::get('/students/id-card/front', [IdCardController::class, 'downloadFront'])->name('students.id-card.front');
        Route::get('/students/id-card/back', [IdCardController::class, 'downloadBack'])->name('students.id-card.back');

        Route::get('/students/tc', [TransferCertificateController::class, 'create'])->name('students.tc.create');
        Route::post('/students/tc', [TransferCertificateController::class, 'store'])->name('students.tc.store');
        Route::get('/students/tc/all', [TransferCertificateController::class, 'index'])->name('students.tc.index');
        Route::get('/students/tc/{transferCertificate}/download', [TransferCertificateController::class, 'download'])->name('students.tc.download');

        Route::get('/students/testimonial', [TestimonialController::class, 'create'])->name('students.testimonial.create');
        Route::post('/students/testimonial', [TestimonialController::class, 'store'])->name('students.testimonial.store');
        Route::get('/students/testimonial/all', [TestimonialController::class, 'index'])->name('students.testimonial.index');
        Route::get('/students/testimonial/{testimonial}/download', [TestimonialController::class, 'download'])->name('students.testimonial.download');

        Route::get('/students/initial-setup/{type}', [InitialSetupController::class, 'index'])->whereIn('type', array_keys(InitialSetupController::TYPES))->name('students.initial-setup');
        Route::post('/students/initial-setup/{type}', [InitialSetupController::class, 'store'])->whereIn('type', array_keys(InitialSetupController::TYPES))->name('students.initial-setup.store');
        Route::put('/students/initial-setup/{type}/{id}', [InitialSetupController::class, 'update'])->whereIn('type', array_keys(InitialSetupController::TYPES))->whereNumber('id')->name('students.initial-setup.update');
        Route::post('/students/initial-setup/{type}/{id}/toggle', [InitialSetupController::class, 'toggleStatus'])->whereIn('type', array_keys(InitialSetupController::TYPES))->whereNumber('id')->name('students.initial-setup.toggle');
        Route::delete('/students/initial-setup/{type}/{id}', [InitialSetupController::class, 'destroy'])->whereIn('type', array_keys(InitialSetupController::TYPES))->whereNumber('id')->name('students.initial-setup.destroy');

        Route::get('/students/configuration/class', [ClassConfigurationController::class, 'index'])->name('students.configuration.class');
        Route::post('/students/configuration/class', [ClassConfigurationController::class, 'store'])->name('students.configuration.class.store');
        Route::post('/students/configuration/class/update-serial', [ClassConfigurationController::class, 'updateSerial'])->name('students.configuration.class.update-serial');
        Route::post('/students/configuration/class/{section}/toggle', [ClassConfigurationController::class, 'toggleStatus'])->name('students.configuration.class.toggle');
        Route::post('/students/configuration/class/{section}/remove-shift', [ClassConfigurationController::class, 'destroy'])->name('students.configuration.class.destroy');

        Route::get('/students/configuration/group', [GroupConfigurationController::class, 'index'])->name('students.configuration.group');
        Route::post('/students/configuration/group', [GroupConfigurationController::class, 'store'])->name('students.configuration.group.store');
        Route::post('/students/configuration/group/update', [GroupConfigurationController::class, 'update'])->name('students.configuration.group.update');
        Route::post('/students/configuration/group/{classGroupAssignment}/toggle', [GroupConfigurationController::class, 'toggleStatus'])->name('students.configuration.group.toggle');
        Route::post('/students/configuration/group/{classGroupAssignment}/delete', [GroupConfigurationController::class, 'destroy'])->name('students.configuration.group.destroy');

        Route::resource('students', StudentController::class);
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

        Route::get('/student-attendance/input', [StudentAttendanceController::class, 'input'])->name('student-attendance.input');
        Route::post('/student-attendance/input', [StudentAttendanceController::class, 'inputStore'])->name('student-attendance.input.store');
        Route::get('/student-attendance/update', [StudentAttendanceController::class, 'update'])->name('student-attendance.update');
        Route::post('/student-attendance/update', [StudentAttendanceController::class, 'updateStore'])->name('student-attendance.update.store');
        Route::get('/student-attendance/leave', [StudentAttendanceController::class, 'leave'])->name('student-attendance.leave');
        Route::post('/student-attendance/leave', [StudentAttendanceController::class, 'leaveStore'])->name('student-attendance.leave.store');
        Route::get('/student-attendance/report/all-section-summary', [AttendanceReportController::class, 'allSectionSummary'])->name('student-attendance.report.all-section-summary');
        Route::get('/student-attendance/report/section-details-day', [AttendanceReportController::class, 'sectionDetailsDay'])->name('student-attendance.report.section-details-day');
        Route::get('/student-attendance/report/section-details-month', [AttendanceReportController::class, 'sectionDetailsMonth'])->name('student-attendance.report.section-details-month');
        Route::get('/student-attendance/report/monthly-summary', [AttendanceReportController::class, 'monthlySummary'])->name('student-attendance.report.monthly-summary');
        Route::get('/student-attendance/report/device-data', [AttendanceReportController::class, 'deviceData'])->name('student-attendance.report.device-data');

        Route::get('/student-attendance/settings/period', [AttendanceSettingsController::class, 'periodSetup'])->name('student-attendance.settings.period');
        Route::post('/student-attendance/settings/period', [AttendanceSettingsController::class, 'periodSetupStore'])->name('student-attendance.settings.period.store');
        Route::get('/student-attendance/settings/time-config', [AttendanceSettingsController::class, 'timeConfig'])->name('student-attendance.settings.time-config');
        Route::post('/student-attendance/settings/time-config', [AttendanceSettingsController::class, 'timeConfigStore'])->name('student-attendance.settings.time-config.store');
        Route::get('/student-attendance/settings/manual-process', [AttendanceSettingsController::class, 'manualProcess'])->name('student-attendance.settings.manual-process');
        Route::post('/student-attendance/settings/manual-process', [AttendanceSettingsController::class, 'manualProcessStore'])->name('student-attendance.settings.manual-process.store');

        Route::get('/hr-attendance/take-attendance', [StaffAttendanceController::class, 'takeAttendance'])->name('hr-attendance.take-attendance');
        Route::post('/hr-attendance/take-attendance', [StaffAttendanceController::class, 'takeAttendanceStore'])->name('hr-attendance.take-attendance.store');
        Route::get('/hr-attendance/update-attendance', [StaffAttendanceController::class, 'updateAttendance'])->name('hr-attendance.update-attendance');
        Route::post('/hr-attendance/update-attendance', [StaffAttendanceController::class, 'updateAttendanceStore'])->name('hr-attendance.update-attendance.store');
        Route::get('/hr-attendance/report/staff-in-out-time', [StaffAttendanceController::class, 'reportStaffInOutTime'])->name('hr-attendance.report.staff-in-out-time');
        Route::get('/hr-attendance/report/date-wise', [StaffAttendanceController::class, 'reportDateWise'])->name('hr-attendance.report.date-wise');
        Route::get('/hr-attendance/report/month-wise', [StaffAttendanceController::class, 'reportMonthWise'])->name('hr-attendance.report.month-wise');
        Route::get('/hr-attendance/report/month-wise/excel', [StaffAttendanceController::class, 'reportMonthWiseExcel'])->name('hr-attendance.report.month-wise.excel');
        Route::get('/hr-attendance/leave-input', [StaffAttendanceController::class, 'leaveInput'])->name('hr-attendance.leave-input');
        Route::post('/hr-attendance/leave-input', [StaffAttendanceController::class, 'leaveInputStore'])->name('hr-attendance.leave-input.store');
        Route::delete('/hr-attendance/leave-input/{leave}', [StaffAttendanceController::class, 'leaveInputDestroy'])->name('hr-attendance.leave-input.destroy');
        Route::get('/hr-attendance/setting/time-configuration', [StaffAttendanceController::class, 'settingTimeConfiguration'])->name('hr-attendance.setting.time-configuration');
        Route::post('/hr-attendance/setting/time-configuration', [StaffAttendanceController::class, 'settingTimeConfigurationStore'])->name('hr-attendance.setting.time-configuration.store');
        Route::get('/hr-attendance/setting/machine-id-mapping', [StaffAttendanceController::class, 'settingMachineIdMapping'])->name('hr-attendance.setting.machine-id-mapping');
        Route::post('/hr-attendance/setting/machine-id-mapping/{teacher}/update', [StaffAttendanceController::class, 'settingMachineIdMappingUpdate'])->name('hr-attendance.setting.machine-id-mapping.update');
        Route::post('/hr-attendance/setting/machine-id-mapping/batch-update', [StaffAttendanceController::class, 'settingMachineIdMappingBatchUpdate'])->name('hr-attendance.setting.machine-id-mapping.batch-update');

        Route::resource('grades', GradeController::class)->except('show');

        Route::resource('exams', ExamController::class);
        Route::get('/exams/{exam}/subjects/create', [ExamSubjectController::class, 'create'])->name('exams.subjects.create');
        Route::post('/exams/{exam}/subjects', [ExamSubjectController::class, 'store'])->name('exams.subjects.store');
        Route::delete('/exams/{exam}/subjects/{examSubject}', [ExamSubjectController::class, 'destroy'])->name('exams.subjects.destroy');

        Route::get('/exam-subjects/{examSubject}/marks', [MarkController::class, 'edit'])->name('marks.edit');
        Route::put('/exam-subjects/{examSubject}/marks', [MarkController::class, 'update'])->name('marks.update');
        Route::get('/exam-subjects/search', [ExamSubjectSearchController::class, 'index'])->name('exam-subjects.search');

        Route::get('/mark-input', [MarkInputController::class, 'index'])->name('mark-input.index');
        Route::put('/mark-input/{examSubject}', [MarkInputController::class, 'update'])->name('mark-input.update');
        Route::get('/mark-input/template/download', [MarkInputController::class, 'downloadTemplate'])->name('mark-input.template');
        Route::post('/mark-input/upload', [MarkInputController::class, 'upload'])->name('mark-input.upload');

        Route::post('/subject-components', [SubjectComponentController::class, 'store'])->name('subject-components.store');
        Route::delete('/subject-components/{subjectComponent}', [SubjectComponentController::class, 'destroy'])->name('subject-components.destroy');

        Route::get('/results', [ResultController::class, 'index'])->name('results.index');
        Route::get('/results/{exam}/marksheet/{student}', [ResultController::class, 'marksheet'])->name('results.marksheet');

        Route::get('/results-process/general-exam', [ResultProcessController::class, 'generalExam'])->name('results-process.general-exam');
        Route::post('/results-process/general-exam/process', [ResultProcessController::class, 'processGeneralExam'])->name('results-process.general-exam.process');

        Route::get('/results-process/grand-final', [GrandFinalController::class, 'processPage'])->name('results-process.grand-final');
        Route::post('/results-process/grand-final/process', [GrandFinalController::class, 'processClass'])->name('results-process.grand-final.process');
        Route::get('/exam-configuration', [GrandFinalController::class, 'index'])->name('exam-configuration.index');
        Route::post('/exam-configuration', [GrandFinalController::class, 'store'])->name('exam-configuration.store');
        Route::delete('/exam-configuration/{grandFinal}', [GrandFinalController::class, 'destroy'])->name('exam-configuration.destroy');
        Route::put('/exam-configuration/{grandFinal}/merit-type', [GrandFinalController::class, 'updateMeritType'])->name('exam-configuration.update-merit-type');
        Route::get('/exam-configuration/grand-final', [GrandFinalController::class, 'markConfigIndex'])->name('exam-configuration.grand-final');
        Route::post('/exam-configuration/grand-final', [GrandFinalController::class, 'storeMarkConfig'])->name('exam-configuration.grand-final.store');

        Route::get('/exam-configuration/subject', [SubjectConfigurationController::class, 'index'])->name('exam-configuration.subject');
        Route::post('/exam-configuration/subject', [SubjectConfigurationController::class, 'store'])->name('exam-configuration.subject.store');
        Route::put('/exam-configuration/subject', [SubjectConfigurationController::class, 'update'])->name('exam-configuration.subject.update');
        Route::delete('/exam-configuration/subject/{subjectAssignment}', [SubjectConfigurationController::class, 'destroy'])->name('exam-configuration.subject.destroy');

        Route::get('/exam-configuration/mark', [MarkConfigurationController::class, 'index'])->name('exam-configuration.mark');
        Route::post('/exam-configuration/mark', [MarkConfigurationController::class, 'store'])->name('exam-configuration.mark.store');

        Route::get('/exam-configuration/marksheet-template', [MarksheetTemplateController::class, 'index'])->name('exam-configuration.marksheet-template');
        Route::post('/exam-configuration/marksheet-template', [MarksheetTemplateController::class, 'store'])->name('exam-configuration.marksheet-template.store');
        Route::delete('/exam-configuration/marksheet-template/{marksheetTemplateConfig}', [MarksheetTemplateController::class, 'destroy'])->name('exam-configuration.marksheet-template.destroy');
        Route::get('/exam-configuration/marksheet-template/preview/{number}', [MarksheetTemplateController::class, 'preview'])->whereNumber('number')->name('exam-configuration.marksheet-template.preview');

        Route::get('/exam-configuration/fourth-subject', [FourthSubjectController::class, 'index'])->name('exam-configuration.fourth-subject');
        Route::post('/exam-configuration/fourth-subject', [FourthSubjectController::class, 'store'])->name('exam-configuration.fourth-subject.store');
        Route::delete('/exam-configuration/fourth-subject/{fourthSubjectAssignment}', [FourthSubjectController::class, 'destroy'])->name('exam-configuration.fourth-subject.destroy');

        Route::get('/exam-configuration/remarks-template', [RemarksTemplateController::class, 'index'])->name('exam-configuration.remarks-template');
        Route::post('/exam-configuration/remarks-template', [RemarksTemplateController::class, 'store'])->name('exam-configuration.remarks-template.store');
        Route::put('/exam-configuration/remarks-template/{remarkTemplate}', [RemarksTemplateController::class, 'update'])->name('exam-configuration.remarks-template.update');
        Route::delete('/exam-configuration/remarks-template/{remarkTemplate}', [RemarksTemplateController::class, 'destroy'])->name('exam-configuration.remarks-template.destroy');

        Route::get('/results-process/merit-position', [ResultProcessController::class, 'meritPosition'])->name('results-process.merit-position');

        Route::get('/results-process/give-remarks', [ResultProcessController::class, 'giveRemarks'])->name('results-process.give-remarks');
        Route::post('/results-process/give-remarks', [ResultProcessController::class, 'saveRemarks'])->name('results-process.give-remarks.store');
        Route::post('/results-process/remark-templates', [ResultProcessController::class, 'storeRemarkTemplate'])->name('results-process.remark-templates.store');

        Route::get('/mark-sheets/general-exam', [MarkSheetController::class, 'generalExam'])->name('mark-sheets.general-exam');
        Route::get('/mark-sheets/general-exam/download', [MarkSheetController::class, 'downloadGeneralExam'])->name('mark-sheets.general-exam.download');
        Route::get('/mark-sheets/grand-final', [MarkSheetController::class, 'grandFinal'])->name('mark-sheets.grand-final');
        Route::get('/mark-sheets/grand-final/{resultBatch}/download', [MarkSheetController::class, 'downloadGrandFinal'])->name('mark-sheets.grand-final.download');

        Route::get('/reports/unassigned-marks', [ReportController::class, 'unassignedMarks'])->name('reports.unassigned-marks');
        Route::get('/reports/section-result', [ReportController::class, 'sectionResult'])->name('reports.section-result');
        Route::get('/reports/class-merit', [ReportController::class, 'classMerit'])->name('reports.class-merit');
        Route::get('/reports/section-merit', [ReportController::class, 'sectionMerit'])->name('reports.section-merit');
        Route::get('/reports/class-failed', [ReportController::class, 'classFailed'])->name('reports.class-failed');
        Route::get('/reports/section-failed', [ReportController::class, 'sectionFailed'])->name('reports.section-failed');
        Route::get('/reports/grading-summary', [ReportController::class, 'sectionWiseGradingSummary'])->name('reports.grading-summary');
        Route::get('/reports/pass-fail-summary', [ReportController::class, 'sectionWisePassFailSummary'])->name('reports.pass-fail-summary');

        Route::get('/exams-seat-plan', [SeatPlanController::class, 'index'])->name('exams.seat-plan');
        Route::get('/exams-seat-plan/admit-card', [SeatPlanController::class, 'downloadAdmitCard'])->name('exams.seat-plan.admit-card');
        Route::get('/exams-seat-plan/seats/{perPage}', [SeatPlanController::class, 'downloadSeatPlan'])->whereNumber('perPage')->name('exams.seat-plan.seats');
        Route::post('/exams-seat-plan/template', [SeatPlanController::class, 'selectTemplate'])->name('exams.seat-plan.select-template');

        Route::get('/exams-top-sheet', [TopSheetController::class, 'index'])->name('exams.top-sheet');
        Route::get('/exams-top-sheet/download', [TopSheetController::class, 'download'])->name('exams.top-sheet.download');

        Route::get('/exams-tabulation-sheet', [TabulationSheetController::class, 'index'])->name('exams.tabulation-sheet');
        Route::get('/exams-tabulation-sheet/download', [TabulationSheetController::class, 'download'])->name('exams.tabulation-sheet.download');

        Route::get('/exams-settings', [ExamSettingsController::class, 'index'])->name('exams.settings');
        Route::post('/exam-short-codes', [ExamShortCodeController::class, 'store'])->name('exam-short-codes.store');
        Route::delete('/exam-short-codes/{examShortCode}', [ExamShortCodeController::class, 'destroy'])->name('exam-short-codes.destroy');

        Route::get('/exams-admit-instruction', [AdmitInstructionController::class, 'index'])->name('exams.admit-instruction');
        Route::post('/exams-admit-instruction', [AdmitInstructionController::class, 'store'])->name('exams.admit-instruction.store');

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

        Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
        Route::put('/accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
        Route::delete('/accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');

        Route::get('/journal-entries', [JournalEntryController::class, 'index'])->name('journal-entries.index');
        Route::get('/journal-entries/create', [JournalEntryController::class, 'create'])->name('journal-entries.create');
        Route::post('/journal-entries', [JournalEntryController::class, 'store'])->name('journal-entries.store');
        Route::get('/journal-entries/{journalEntry}', [JournalEntryController::class, 'show'])->name('journal-entries.show');
        Route::post('/journal-entries/{journalEntry}/reverse', [JournalEntryController::class, 'reverse'])->name('journal-entries.reverse');

        Route::get('/accounting-reports/income-expense', [AccountingReportController::class, 'incomeExpense'])->name('accounting-reports.income-expense');
        Route::get('/accounting-reports/cash-book', [AccountingReportController::class, 'cashBook'])->name('accounting-reports.cash-book');
        Route::get('/accounting-reports/bank-book', [AccountingReportController::class, 'bankBook'])->name('accounting-reports.bank-book');
        Route::get('/accounting-reports/ledger', [AccountingReportController::class, 'ledger'])->name('accounting-reports.ledger');
        Route::get('/accounting-reports/trial-balance', [AccountingReportController::class, 'trialBalance'])->name('accounting-reports.trial-balance');
        Route::get('/accounting-reports/profit-loss', [AccountingReportController::class, 'profitAndLoss'])->name('accounting-reports.profit-loss');
        Route::get('/accounting-reports/balance-sheet', [AccountingReportController::class, 'balanceSheet'])->name('accounting-reports.balance-sheet');
        Route::get('/accounting-reports/fee-collection', [AccountingReportController::class, 'feeCollection'])->name('accounting-reports.fee-collection');

        Route::resource('notices', NoticeController::class)->except('show');

        Route::get('/sms-wallet', [SmsWalletController::class, 'index'])->name('sms-wallet.index');
        Route::get('/sms-wallet/buy', [SmsCreditOrderController::class, 'create'])->name('sms-credit-orders.create');
        Route::post('/sms-wallet/buy', [SmsCreditOrderController::class, 'store'])->name('sms-credit-orders.store');

        Route::get('/sms-send/guardian', [GuardianSmsController::class, 'index'])->name('sms-send.guardian.index');
        Route::post('/sms-send/guardian', [GuardianSmsController::class, 'store'])->name('sms-send.guardian.store');

        Route::get('/sms-send/hr', [HrSmsController::class, 'index'])->name('sms-send.hr.index');
        Route::post('/sms-send/hr', [HrSmsController::class, 'store'])->name('sms-send.hr.store');

        Route::get('/sms-send/dynamic', [DynamicSmsController::class, 'index'])->name('sms-send.dynamic.index');
        Route::post('/sms-send/dynamic', [DynamicSmsController::class, 'store'])->name('sms-send.dynamic.store');

        Route::get('/sms-send/result', [ResultSmsController::class, 'index'])->name('sms-send.result.index');
        Route::post('/sms-send/result', [ResultSmsController::class, 'store'])->name('sms-send.result.store');

        Route::get('/sms-send/due-fee', [DueFeeSmsController::class, 'index'])->name('sms-send.due-fee.index');
        Route::post('/sms-send/due-fee', [DueFeeSmsController::class, 'store'])->name('sms-send.due-fee.store');

        Route::get('/sms-send/attendance', [AttendanceSmsController::class, 'index'])->name('sms-send.attendance.index');
        Route::post('/sms-send/attendance', [AttendanceSmsController::class, 'store'])->name('sms-send.attendance.store');

        Route::get('/sms-send/birthday', [BirthdaySmsController::class, 'index'])->name('sms-send.birthday.index');
        Route::post('/sms-send/birthday', [BirthdaySmsController::class, 'store'])->name('sms-send.birthday.store');

        Route::get('/sms-reports/date-wise', [SmsReportController::class, 'dateWise'])->name('sms-reports.date-wise');
        Route::get('/sms-reports/purchases', [SmsReportController::class, 'purchases'])->name('sms-reports.purchases');

        Route::get('/sms-templates', [SmsTemplateController::class, 'index'])->name('sms-templates.index');
        Route::post('/sms-templates', [SmsTemplateController::class, 'store'])->name('sms-templates.store');
        Route::put('/sms-templates/{smsTemplate}', [SmsTemplateController::class, 'update'])->name('sms-templates.update');
        Route::delete('/sms-templates/{smsTemplate}', [SmsTemplateController::class, 'destroy'])->name('sms-templates.destroy');

        Route::get('/school-profile', [SchoolProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/school-profile', [SchoolProfileController::class, 'update'])->name('profile.update');

        Route::get('/signatures', [SignatureController::class, 'index'])->name('signatures.index');
        Route::post('/signatures', [SignatureController::class, 'store'])->name('signatures.store');
        Route::post('/signatures/{signature}/image', [SignatureController::class, 'uploadImage'])->name('signatures.upload-image');
        Route::delete('/signatures/{signature}', [SignatureController::class, 'destroy'])->name('signatures.destroy');

        Route::get('/users', [SchoolUserController::class, 'index'])->name('users.index');
        Route::post('/users', [SchoolUserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/toggle-status', [SchoolUserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::delete('/users/{user}', [SchoolUserController::class, 'destroy'])->name('users.destroy');

        Route::post('/students/{student}/portal-account', [StudentPortalAccountController::class, 'store'])->name('students.portal-account.store');
        Route::delete('/students/{student}/portal-account', [StudentPortalAccountController::class, 'destroy'])->name('students.portal-account.destroy');

        // Library
        Route::resource('book-categories', BookCategoryController::class)->only(['index', 'store', 'destroy']);
        Route::resource('books', BookController::class)->except('show');
        Route::get('/book-issues', [BookIssueController::class, 'index'])->name('book-issues.index');
        Route::get('/book-issues/create', [BookIssueController::class, 'create'])->name('book-issues.create');
        Route::post('/book-issues', [BookIssueController::class, 'store'])->name('book-issues.store');
        Route::post('/book-issues/{bookIssue}/return', [BookIssueController::class, 'returnBook'])->name('book-issues.return');

        // Transport
        Route::resource('vehicles', VehicleController::class)->except('show');
        Route::resource('transport-routes', TransportRouteController::class)->except('show');
        Route::post('/transport-routes/{route}/stops', [RouteStopController::class, 'store'])->name('transport-routes.stops.store');
        Route::delete('/transport-routes/{route}/stops/{stop}', [RouteStopController::class, 'destroy'])->name('transport-routes.stops.destroy');
        Route::get('/student-transports', [StudentTransportController::class, 'index'])->name('student-transports.index');
        Route::get('/student-transports/create', [StudentTransportController::class, 'create'])->name('student-transports.create');
        Route::post('/student-transports', [StudentTransportController::class, 'store'])->name('student-transports.store');
        Route::delete('/student-transports/{studentTransport}', [StudentTransportController::class, 'destroy'])->name('student-transports.destroy');

        // Inventory
        Route::resource('inventory-categories', InventoryCategoryController::class)->only(['index', 'store', 'destroy']);
        Route::resource('inventory-items', InventoryItemController::class)->except('show');
        Route::post('/inventory-items/{item}/transactions', [InventoryTransactionController::class, 'store'])->name('inventory-items.transactions.store');

        // Payroll / HR
        Route::get('/salary-structures', [SalaryStructureController::class, 'index'])->name('salary-structures.index');
        Route::post('/salary-structures', [SalaryStructureController::class, 'store'])->name('salary-structures.store');
        Route::delete('/salary-structures/{salaryStructure}', [SalaryStructureController::class, 'destroy'])->name('salary-structures.destroy');
        Route::get('/payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
        Route::post('/payrolls/generate', [PayrollController::class, 'generate'])->name('payrolls.generate');
        Route::post('/payrolls/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('payrolls.mark-paid');
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
