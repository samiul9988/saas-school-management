<?php

namespace Tests\Feature\Sms;

use App\Exceptions\Sms\InsufficientCentralCreditsException;
use App\Exceptions\Sms\InsufficientSchoolCreditsException;
use App\Models\School;
use App\Models\SchoolSmsWallet;
use App\Models\SmsGatewaySetting;
use App\Models\User;
use App\Services\Sms\SmsSendingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class SmsSendingServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchool(int $walletBalance): School
    {
        $school = School::create([
            'name' => 'Test School',
            'slug' => Str::slug('Test School').'-'.Str::random(5),
            'email' => fake()->unique()->safeEmail(),
            'status' => 'active',
        ]);

        SchoolSmsWallet::withoutGlobalScope('school')->create([
            'school_id' => $school->id,
            'balance' => $walletBalance,
        ]);

        return $school;
    }

    private function configureGateway(int $centralBalance): void
    {
        SmsGatewaySetting::current()->update([
            'api_key' => 'test-key',
            'sender_id' => 'TESTSENDER',
            'central_balance' => $centralBalance,
            'price_per_sms' => 0.5,
        ]);
    }

    public function test_successful_send_debits_school_and_central_balances(): void
    {
        $school = $this->makeSchool(10);
        $this->configureGateway(100);

        Http::fake([
            '*' => Http::response(['response_code' => 202, 'message_id' => 'abc123']),
        ]);

        $service = app(SmsSendingService::class);
        $sendRequest = $service->send($school, ['01700000000'], 'Hello there', (string) Str::uuid());

        $this->assertSame('sent', $sendRequest->status);
        $this->assertSame(1, $sendRequest->segments);
        $this->assertSame(1, $sendRequest->credits_required);

        $this->assertSame(9, SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $school->id)->first()->balance);
        $this->assertSame(99, SmsGatewaySetting::current()->central_balance);
    }

    public function test_multi_recipient_multi_segment_credit_math(): void
    {
        $school = $this->makeSchool(20);
        $this->configureGateway(100);

        Http::fake(['*' => Http::response(['response_code' => 202])]);

        $longMessage = str_repeat('a', 200); // GSM-7, 200 chars => 2 segments (153/seg)

        $service = app(SmsSendingService::class);
        $sendRequest = $service->send($school, ['01700000001', '01700000002'], $longMessage, (string) Str::uuid());

        $this->assertSame(2, $sendRequest->segments);
        $this->assertSame(4, $sendRequest->credits_required); // 2 segments * 2 recipients

        $this->assertSame(16, SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $school->id)->first()->balance);
    }

    public function test_insufficient_school_credits_throws_and_does_not_debit(): void
    {
        $school = $this->makeSchool(0);
        $this->configureGateway(100);

        Http::fake();

        $service = app(SmsSendingService::class);

        $this->expectException(InsufficientSchoolCreditsException::class);

        try {
            $service->send($school, ['01700000000'], 'Hi', (string) Str::uuid());
        } finally {
            Http::assertNothingSent();
            $this->assertSame(0, SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $school->id)->first()->balance);
            $this->assertSame(100, SmsGatewaySetting::current()->central_balance);
        }
    }

    public function test_insufficient_central_credits_blocks_send_even_with_school_balance(): void
    {
        $school = $this->makeSchool(1000);
        $this->configureGateway(0);

        Http::fake();

        $service = app(SmsSendingService::class);

        $this->expectException(InsufficientCentralCreditsException::class);

        try {
            $service->send($school, ['01700000000'], 'Hi', (string) Str::uuid());
        } finally {
            Http::assertNothingSent();
            $this->assertSame(1000, SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $school->id)->first()->balance);
        }
    }

    public function test_failed_send_reverses_reservation(): void
    {
        $school = $this->makeSchool(10);
        $this->configureGateway(100);

        Http::fake(['*' => Http::response(['response_code' => 1001, 'error_message' => 'Invalid number'])]);

        $service = app(SmsSendingService::class);
        $sendRequest = $service->send($school, ['bad-number'], 'Hi', (string) Str::uuid());

        $this->assertSame('failed', $sendRequest->status);
        $this->assertSame(10, SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $school->id)->first()->balance);
        $this->assertSame(100, SmsGatewaySetting::current()->central_balance);
    }

    public function test_idempotent_replay_does_not_double_debit(): void
    {
        $school = $this->makeSchool(10);
        $this->configureGateway(100);

        Http::fake(['*' => Http::response(['response_code' => 202])]);

        $key = (string) Str::uuid();
        $service = app(SmsSendingService::class);

        $first = $service->send($school, ['01700000000'], 'Hi', $key);
        $second = $service->send($school, ['01700000000'], 'Hi', $key);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(9, SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $school->id)->first()->balance);
        Http::assertSentCount(1);
    }

    public function test_tenant_isolation_scopes_wallet_queries_to_authenticated_school(): void
    {
        $schoolA = $this->makeSchool(5);
        $schoolB = $this->makeSchool(7);

        $userA = User::create([
            'name' => 'Admin A',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $schoolA->id,
        ]);

        $this->actingAs($userA);

        $visibleWallets = SchoolSmsWallet::all();

        $this->assertCount(1, $visibleWallets);
        $this->assertSame($schoolA->id, $visibleWallets->first()->school_id);
        $this->assertNotEquals($schoolB->id, $visibleWallets->first()->school_id);
    }
}
