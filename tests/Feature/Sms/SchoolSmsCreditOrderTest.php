<?php

namespace Tests\Feature\Sms;

use App\Models\School;
use App\Models\SchoolSmsCreditOrder;
use App\Models\SchoolSmsWallet;
use App\Models\SmsGatewaySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class SchoolSmsCreditOrderTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Order Test School',
            'slug' => Str::slug('Order Test School').'-'.Str::random(5),
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

    private function makeSuperAdmin(): User
    {
        return User::create([
            'name' => 'Super Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
    }

    public function test_school_can_submit_credit_order_without_calling_gateway(): void
    {
        $admin = $this->makeSchoolAdmin();
        SmsGatewaySetting::current()->update(['price_per_sms' => 0.5]);

        Http::fake();

        $response = $this->actingAs($admin)->post(route('school.sms-credit-orders.store'), [
            'credits' => 1000,
            'method' => 'manual',
        ]);

        $response->assertRedirect();
        Http::assertNothingSent();

        $order = SchoolSmsCreditOrder::withoutGlobalScope('school')->first();
        $this->assertSame('pending', $order->status);
        $this->assertSame(1000, $order->credits);
        $this->assertEquals(500.0, (float) $order->amount);
    }

    public function test_super_admin_approving_order_credits_wallet_without_touching_central_ledger(): void
    {
        $admin = $this->makeSchoolAdmin();
        $superAdmin = $this->makeSuperAdmin();
        SmsGatewaySetting::current()->update(['price_per_sms' => 0.5, 'central_balance' => 50]);

        $order = SchoolSmsCreditOrder::create([
            'school_id' => $admin->school_id,
            'credits' => 2000,
            'price_per_sms' => 0.5,
            'amount' => 1000,
            'method' => 'manual',
            'status' => 'pending',
            'requested_by' => $admin->id,
        ]);

        $this->actingAs($superAdmin)->post(route('super-admin.sms-credit-orders.approve', $order));

        $order->refresh();
        $wallet = SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $admin->school_id)->first();

        $this->assertSame('approved', $order->status);
        $this->assertSame(2000, $wallet->balance);
        $this->assertSame(50, SmsGatewaySetting::current()->central_balance, 'School purchases must never inflate the central provider balance.');
    }

    public function test_school_wallet_and_order_pages_render(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->get(route('school.sms-wallet.index'))->assertOk();
        $this->actingAs($admin)->get(route('school.sms-credit-orders.create'))->assertOk();
    }

    public function test_super_admin_sms_pages_render(): void
    {
        $this->makeSchoolAdmin();
        $superAdmin = $this->makeSuperAdmin();

        $this->actingAs($superAdmin)->get(route('super-admin.sms-gateway.edit'))->assertOk();
        $this->actingAs($superAdmin)->get(route('super-admin.sms-credit-purchases.index'))->assertOk();
        $this->actingAs($superAdmin)->get(route('super-admin.sms-credit-purchases.create'))->assertOk();
        $this->actingAs($superAdmin)->get(route('super-admin.sms-schools.index'))->assertOk();
        $this->actingAs($superAdmin)->get(route('super-admin.sms-credit-orders.index'))->assertOk();
        $this->actingAs($superAdmin)->get(route('super-admin.sms-usage-report.index'))->assertOk();

        $school = School::first();
        $this->actingAs($superAdmin)->get(route('super-admin.sms-schools.show', $school))->assertOk();
    }

    public function test_rejecting_order_does_not_change_wallet(): void
    {
        $admin = $this->makeSchoolAdmin();
        $superAdmin = $this->makeSuperAdmin();

        $order = SchoolSmsCreditOrder::create([
            'school_id' => $admin->school_id,
            'credits' => 500,
            'price_per_sms' => 0.5,
            'amount' => 250,
            'method' => 'manual',
            'status' => 'pending',
            'requested_by' => $admin->id,
        ]);

        $this->actingAs($superAdmin)->post(route('super-admin.sms-credit-orders.reject', $order));

        $order->refresh();
        $wallet = SchoolSmsWallet::withoutGlobalScope('school')->where('school_id', $admin->school_id)->first();

        $this->assertSame('rejected', $order->status);
        $this->assertNull($wallet);
    }
}
