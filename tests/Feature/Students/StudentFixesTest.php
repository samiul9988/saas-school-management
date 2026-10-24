<?php

namespace Tests\Feature\Students;

use App\Models\AcademicYear;
use App\Models\ClassGroupAssignment;
use App\Models\FeeType;
use App\Models\Group;
use App\Models\ResultBatch;
use App\Models\ResultBatchResult;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolSmsWallet;
use App\Models\Section;
use App\Models\SmsGatewaySetting;
use App\Models\SmsSendRequest;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class StudentFixesTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Student Fixes Test School',
            'slug' => Str::slug('Student Fixes Test School').'-'.Str::random(5),
            'email' => fake()->unique()->safeEmail(),
            'status' => 'active',
        ]);

        return User::create([
            'name' => 'School Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school->id,
        ]);
    }

    public function test_welcome_sms_is_sent_on_student_admission_using_configured_template(): void
    {
        $admin = $this->makeSchoolAdmin();
        SchoolSmsWallet::withoutGlobalScope('school')->create(['school_id' => $admin->school_id, 'balance' => 10]);
        SmsGatewaySetting::current()->update(['api_key' => 'test-key', 'sender_id' => 'TEST', 'central_balance' => 1000]);
        Http::fake(['*' => Http::response(['response_code' => 202])]);

        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 1']);

        $response = $this->actingAs($admin)->post(route('school.students.store'), [
            'name' => 'New Student',
            'school_class_id' => $class->id,
            'guardian_phone' => '01700000000',
            'status' => 'active',
            'send_welcome_sms' => '1',
            'welcome_sms_content' => 'Welcome to {{school_name}}! Admission confirmed for {{student_name}} in Class {{class}}. ID: {{student_id}}. Class starts on {{date}}. Thank you.',
        ]);

        $response->assertRedirect(route('school.students.index'));

        $student = Student::first();
        $sendRequest = SmsSendRequest::withoutGlobalScope('school')->where('context', 'admission_sms')->first();

        $this->assertNotNull($sendRequest);
        $this->assertStringContainsString('New Student', $sendRequest->message);
        $this->assertStringContainsString('Class 1', $sendRequest->message);
        $this->assertStringNotContainsString('{{', $sendRequest->message);
    }

    public function test_no_sms_sent_when_checkbox_unchecked(): void
    {
        $admin = $this->makeSchoolAdmin();
        SchoolSmsWallet::withoutGlobalScope('school')->create(['school_id' => $admin->school_id, 'balance' => 10]);
        Http::fake();

        $this->actingAs($admin)->post(route('school.students.store'), [
            'name' => 'No SMS Student',
            'guardian_phone' => '01700000000',
            'status' => 'active',
        ]);

        Http::assertNothingSent();
        $this->assertSame(0, SmsSendRequest::withoutGlobalScope('school')->where('context', 'admission_sms')->count());
    }

    public function test_student_group_dropdown_uses_configured_groups_not_hardcoded_list(): void
    {
        $admin = $this->makeSchoolAdmin();
        Group::create(['school_id' => $admin->school_id, 'name' => 'Custom Group X', 'serial' => 1, 'status' => 'enable']);

        $response = $this->actingAs($admin)->get(route('school.students.create'));

        $response->assertOk()->assertSee('Custom Group X')->assertDontSee('Science');
    }

    public function test_student_group_dropdown_falls_back_to_defaults_when_none_configured(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->get(route('school.students.create'));

        $response->assertOk()->assertSee('Science')->assertSee('Commerce');
    }

    public function test_disabled_groups_are_excluded_from_dropdown(): void
    {
        $admin = $this->makeSchoolAdmin();
        Group::create(['school_id' => $admin->school_id, 'name' => 'Hidden Group', 'serial' => 1, 'status' => 'disable']);
        Group::create(['school_id' => $admin->school_id, 'name' => 'Visible Group', 'serial' => 2, 'status' => 'enable']);

        $response = $this->actingAs($admin)->get(route('school.students.create'));

        $response->assertOk()->assertSee('Visible Group')->assertDontSee('Hidden Group');
    }

    public function test_migration_with_merit_updates_academic_year_not_just_roll(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 1']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $oldYear = AcademicYear::create(['school_id' => $admin->school_id, 'name' => '2025', 'starts_on' => '2025-01-01', 'ends_on' => '2025-12-31']);
        $newYear = AcademicYear::create(['school_id' => $admin->school_id, 'name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);

        $student = Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'section_id' => $section->id, 'academic_year_id' => $oldYear->id, 'name' => 'Merit Student', 'roll' => '5', 'status' => 'active']);

        $batch = ResultBatch::create(['school_id' => $admin->school_id, 'type' => 'exam', 'name' => 'Final Exam', 'school_class_id' => $class->id]);
        ResultBatchResult::create(['school_id' => $admin->school_id, 'result_batch_id' => $batch->id, 'student_id' => $student->id, 'school_class_id' => $class->id, 'section_id' => $section->id, 'position' => 1]);

        $this->actingAs($admin)->post(route('school.students.migration-with-merit.store'), [
            'section_id' => $section->id,
            'result_batch_id' => $batch->id,
            'target_academic_year_id' => $newYear->id,
            'student_ids' => [$student->id],
            'rolls' => [$student->id => '1'],
        ]);

        $student->refresh();
        $this->assertSame($newYear->id, $student->academic_year_id);
        $this->assertSame('1', $student->roll);
    }

    public function test_migration_with_merit_requires_target_academic_year(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 1']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $student = Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'section_id' => $section->id, 'name' => 'Student', 'status' => 'active']);
        $batch = ResultBatch::create(['school_id' => $admin->school_id, 'type' => 'exam', 'name' => 'Final Exam', 'school_class_id' => $class->id]);

        $response = $this->actingAs($admin)->post(route('school.students.migration-with-merit.store'), [
            'section_id' => $section->id,
            'result_batch_id' => $batch->id,
            'student_ids' => [$student->id],
        ]);

        $response->assertSessionHasErrors('target_academic_year_id');
    }

    public function test_deleting_student_does_not_crash_even_with_related_fee_records(): void
    {
        // Every FK referencing students cascades on delete in this schema, so this never
        // actually throws today — this guards the destroy() try/catch stays a safe no-op
        // and confirms the delete still completes cleanly end-to-end.
        $admin = $this->makeSchoolAdmin();
        $student = Student::create(['school_id' => $admin->school_id, 'name' => 'Protected Student', 'status' => 'active']);
        $feeType = FeeType::create(['school_id' => $admin->school_id, 'name' => 'Tuition']);
        StudentFee::create(['school_id' => $admin->school_id, 'student_id' => $student->id, 'fee_type_id' => $feeType->id, 'amount' => 100, 'paid_amount' => 0, 'status' => 'unpaid']);

        $response = $this->actingAs($admin)->delete(route('school.students.destroy', $student));

        $response->assertRedirect(route('school.students.index'));
        $this->assertSame('Student removed.', session('status'));
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('student_fees', ['student_id' => $student->id]);
    }

    public function test_student_without_related_records_can_be_deleted(): void
    {
        $admin = $this->makeSchoolAdmin();
        $student = Student::create(['school_id' => $admin->school_id, 'name' => 'Deletable Student', 'status' => 'active']);

        $this->actingAs($admin)->delete(route('school.students.destroy', $student));

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_enable_disable_confirmation_prompt_present_in_markup(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 1']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        Student::create(['school_id' => $admin->school_id, 'section_id' => $section->id, 'name' => 'Student', 'status' => 'active']);

        $response = $this->actingAs($admin)->get(route('school.students.enable-disable', ['section_id' => $section->id, 'mode' => 'disable']));

        $response->assertOk()->assertSee('onsubmit="return confirm(', false);
    }

    public function test_class_group_assignment_configuration_is_tenant_isolated(): void
    {
        $adminA = $this->makeSchoolAdmin();
        $adminB = $this->makeSchoolAdmin();

        $classA = SchoolClass::create(['school_id' => $adminA->school_id, 'name' => 'Class A']);
        $groupA = Group::create(['school_id' => $adminA->school_id, 'name' => 'Group A', 'serial' => 1, 'status' => 'enable']);
        ClassGroupAssignment::create(['school_id' => $adminA->school_id, 'school_class_id' => $classA->id, 'group_id' => $groupA->id]);

        $this->actingAs($adminB);
        $this->assertSame(0, ClassGroupAssignment::count());

        $this->actingAs($adminA);
        $this->assertSame(1, ClassGroupAssignment::count());
    }
}
