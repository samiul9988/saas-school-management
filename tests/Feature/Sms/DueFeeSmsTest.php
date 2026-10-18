<?php

namespace Tests\Feature\Sms;

use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolSmsWallet;
use App\Models\Section;
use App\Models\Shift;
use App\Models\SmsGatewaySetting;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class DueFeeSmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_students_with_outstanding_dues_in_the_selected_shift_are_listed(): void
    {
        $school = School::create([
            'name' => 'Due Fee Test School',
            'slug' => Str::slug('Due Fee Test School').'-'.Str::random(5),
            'email' => fake()->unique()->safeEmail(),
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'School Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school->id,
        ]);

        $year = AcademicYear::create(['school_id' => $school->id, 'name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);
        $class = SchoolClass::create(['school_id' => $school->id, 'name' => 'Class 1']);
        $shift = Shift::create(['school_id' => $school->id, 'name' => 'Morning']);
        $otherShift = Shift::create(['school_id' => $school->id, 'name' => 'Evening']);
        $section = Section::create(['school_id' => $school->id, 'school_class_id' => $class->id, 'shift_id' => $shift->id, 'name' => 'A']);
        $otherSection = Section::create(['school_id' => $school->id, 'school_class_id' => $class->id, 'shift_id' => $otherShift->id, 'name' => 'B']);
        $feeType = FeeType::create(['school_id' => $school->id, 'name' => 'Tuition']);

        $dueStudent = Student::create(['school_id' => $school->id, 'school_class_id' => $class->id, 'section_id' => $section->id, 'name' => 'Due Student', 'guardian_phone' => '01700000001', 'status' => 'active']);
        $paidStudent = Student::create(['school_id' => $school->id, 'school_class_id' => $class->id, 'section_id' => $section->id, 'name' => 'Paid Student', 'guardian_phone' => '01700000002', 'status' => 'active']);
        $otherShiftStudent = Student::create(['school_id' => $school->id, 'school_class_id' => $class->id, 'section_id' => $otherSection->id, 'name' => 'Other Shift Student', 'guardian_phone' => '01700000003', 'status' => 'active']);

        StudentFee::create(['school_id' => $school->id, 'student_id' => $dueStudent->id, 'fee_type_id' => $feeType->id, 'academic_year_id' => $year->id, 'amount' => 500, 'paid_amount' => 0, 'status' => 'unpaid']);
        StudentFee::create(['school_id' => $school->id, 'student_id' => $paidStudent->id, 'fee_type_id' => $feeType->id, 'academic_year_id' => $year->id, 'amount' => 500, 'paid_amount' => 500, 'status' => 'paid']);
        StudentFee::create(['school_id' => $school->id, 'student_id' => $otherShiftStudent->id, 'fee_type_id' => $feeType->id, 'academic_year_id' => $year->id, 'amount' => 300, 'paid_amount' => 0, 'status' => 'unpaid']);

        SchoolSmsWallet::withoutGlobalScope('school')->create(['school_id' => $school->id, 'balance' => 10]);
        SmsGatewaySetting::current()->update(['api_key' => 'test-key', 'sender_id' => 'TEST', 'central_balance' => 1000]);
        Http::fake(['*' => Http::response(['response_code' => 202])]);

        $response = $this->actingAs($admin)->get(route('school.sms-send.due-fee.index', [
            'academic_year_id' => $year->id,
            'shift_id' => $shift->id,
        ]));

        $response->assertOk()->assertSee('Due Student')->assertDontSee('Paid Student')->assertDontSee('Other Shift Student');

        $this->actingAs($admin)->post(route('school.sms-send.due-fee.store'), [
            'student_ids' => [$dueStudent->id],
            'academic_year_id' => $year->id,
            'shift_id' => $shift->id,
            'content' => '{{student_name}} owes {{due_amount}}',
        ]);

        $this->assertSame(9, SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $school->id)->first()->balance);
    }
}
