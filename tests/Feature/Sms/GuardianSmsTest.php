<?php

namespace Tests\Feature\Sms;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolSmsWallet;
use App\Models\SmsGatewaySetting;
use App\Models\SmsSendRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class GuardianSmsTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdminWithClass(int $walletBalance = 10): array
    {
        $school = School::create([
            'name' => 'Guardian SMS Test School',
            'slug' => Str::slug('Guardian SMS Test School').'-'.Str::random(5),
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

        $class = SchoolClass::create(['school_id' => $school->id, 'name' => 'Class 1']);

        SchoolSmsWallet::withoutGlobalScope('school')->create(['school_id' => $school->id, 'balance' => $walletBalance]);
        SmsGatewaySetting::current()->update(['api_key' => 'test-key', 'sender_id' => 'TEST', 'central_balance' => 1000]);

        return [$admin, $class];
    }

    public function test_sending_to_selected_guardians_debits_wallet_once_per_recipient(): void
    {
        [$admin, $class] = $this->makeSchoolAdminWithClass(10);

        $studentA = Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'Student A', 'guardian_phone' => '01700000001', 'status' => 'active']);
        $studentB = Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'Student B', 'guardian_phone' => '01700000002', 'status' => 'active']);

        Http::fake(['*' => Http::response(['response_code' => 202])]);

        $this->actingAs($admin)->post(route('school.sms-send.guardian.store'), [
            'student_ids' => [$studentA->id, $studentB->id],
            'content' => 'Dear parent of {{student_name}}, hello.',
        ]);

        $this->assertSame(8, SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $admin->school_id)->first()->balance);
        $this->assertSame(2, SmsSendRequest::withoutGlobalScope('school')->where('context', 'guardian_sms')->count());

        $messages = SmsSendRequest::withoutGlobalScope('school')->where('context', 'guardian_sms')->pluck('message');
        $this->assertTrue($messages->contains('Dear parent of Student A, hello.'));
        $this->assertTrue($messages->contains('Dear parent of Student B, hello.'));
    }

    public function test_send_stops_cleanly_once_wallet_is_exhausted(): void
    {
        [$admin, $class] = $this->makeSchoolAdminWithClass(1);

        $studentA = Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'Student A', 'guardian_phone' => '01700000001', 'status' => 'active']);
        $studentB = Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'Student B', 'guardian_phone' => '01700000002', 'status' => 'active']);

        Http::fake(['*' => Http::response(['response_code' => 202])]);

        $response = $this->actingAs($admin)->post(route('school.sms-send.guardian.store'), [
            'student_ids' => [$studentA->id, $studentB->id],
            'content' => 'Hi {{student_name}}',
        ]);

        $response->assertRedirect();
        $this->assertSame(0, SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $admin->school_id)->first()->balance);
        $this->assertSame(1, SmsSendRequest::withoutGlobalScope('school')->where('context', 'guardian_sms')->count());
    }

    public function test_guardian_sms_page_renders_with_class_filter(): void
    {
        [$admin, $class] = $this->makeSchoolAdminWithClass();
        Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'Student A', 'guardian_phone' => '01700000001', 'status' => 'active']);

        $this->actingAs($admin)
            ->get(route('school.sms-send.guardian.index', ['school_class_id' => $class->id]))
            ->assertOk()
            ->assertSee('Student A');
    }
}
