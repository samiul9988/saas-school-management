<?php

namespace Tests\Feature\InstituteSettings;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SchoolUserTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'User List Test School',
            'slug' => Str::slug('User List Test School').'-'.Str::random(5),
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

    public function test_school_admin_can_create_operator_user(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->post(route('school.users.store'), [
            'name' => 'Operator One',
            'phone' => '01700000000',
            'email' => 'operator@example.com',
            'password' => 'password123',
            'role' => User::ROLE_SCHOOL_OPERATOR,
        ]);

        $response->assertRedirect(route('school.users.index'));

        $operator = User::where('email', 'operator@example.com')->first();
        $this->assertSame(User::ROLE_SCHOOL_OPERATOR, $operator->role);
        $this->assertSame($admin->school_id, $operator->school_id);
        $this->assertTrue($operator->is_active);
    }

    public function test_newly_created_operator_can_log_in_and_reach_school_context_routes(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->post(route('school.users.store'), [
            'name' => 'Operator One',
            'email' => 'operator2@example.com',
            'password' => 'password123',
            'role' => User::ROLE_SCHOOL_OPERATOR,
        ]);

        $operator = User::where('email', 'operator2@example.com')->first();

        $this->actingAs($operator)->get(route('school.dashboard'))->assertOk();
    }

    public function test_toggle_status_flips_is_active(): void
    {
        $admin = $this->makeSchoolAdmin();
        $operator = User::create([
            'name' => 'Operator',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => User::ROLE_SCHOOL_OPERATOR,
            'school_id' => $admin->school_id,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('school.users.toggle-status', $operator));

        $this->assertFalse($operator->refresh()->is_active);
    }

    public function test_cannot_delete_own_account(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->delete(route('school.users.destroy', $admin));

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_cannot_delete_only_remaining_admin(): void
    {
        $admin = $this->makeSchoolAdmin();
        $operator = User::create([
            'name' => 'Operator',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => User::ROLE_SCHOOL_OPERATOR,
            'school_id' => $admin->school_id,
        ]);

        $this->actingAs($operator)->delete(route('school.users.destroy', $admin));

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_school_cannot_delete_another_schools_user(): void
    {
        $adminA = $this->makeSchoolAdmin();
        $adminB = $this->makeSchoolAdmin();

        $this->actingAs($adminA)->delete(route('school.users.destroy', $adminB))->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $adminB->id]);
    }
}
