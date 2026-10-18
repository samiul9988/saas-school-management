<?php

namespace Tests\Feature\Sms;

use App\Models\Period;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SmsSendPagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Smoke Test School',
            'slug' => Str::slug('Smoke Test School').'-'.Str::random(5),
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

    public function test_hr_sms_page_renders(): void
    {
        $admin = $this->makeSchoolAdmin();
        Teacher::create(['school_id' => $admin->school_id, 'name' => 'Teacher One', 'phone' => '01700000000', 'status' => 'active']);

        $this->actingAs($admin)->get(route('school.sms-send.hr.index'))->assertOk()->assertSee('Teacher One');
    }

    public function test_dynamic_sms_page_renders(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->get(route('school.sms-send.dynamic.index'))->assertOk();
    }

    public function test_result_sms_page_renders(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->get(route('school.sms-send.result.index'))->assertOk();
    }

    public function test_attendance_sms_page_renders(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 1']);
        Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        Period::create(['school_id' => $admin->school_id, 'name' => 'Period 1']);

        $this->actingAs($admin)->get(route('school.sms-send.attendance.index'))->assertOk();
    }

    public function test_birthday_sms_page_renders_and_matches_today(): void
    {
        $admin = $this->makeSchoolAdmin();
        Student::create([
            'school_id' => $admin->school_id,
            'name' => 'Birthday Student',
            'guardian_phone' => '01700000000',
            'date_of_birth' => today()->subYears(10)->format('Y-m-d'),
            'status' => 'active',
        ]);

        $this->actingAs($admin)->get(route('school.sms-send.birthday.index'))->assertOk()->assertSee('Birthday Student');
    }

    public function test_date_wise_report_page_renders(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->get(route('school.sms-reports.date-wise'))->assertOk();
    }

    public function test_purchase_report_page_renders(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->get(route('school.sms-reports.purchases'))->assertOk();
    }
}
