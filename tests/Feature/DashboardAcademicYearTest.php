<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardAcademicYearTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Dashboard Academic Year Test School',
            'slug' => Str::slug('Dashboard Academic Year Test School').'-'.Str::random(5),
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

    public function test_dashboard_shows_nothing_until_an_academic_year_is_explicitly_selected(): void
    {
        $admin = $this->makeSchoolAdmin();
        AcademicYear::create(['school_id' => $admin->school_id, 'name' => '2027', 'starts_on' => '2027-01-01', 'ends_on' => '2027-12-31']);

        $response = $this->actingAs($admin)->get(route('school.dashboard'));

        $response->assertOk()->assertSee('Academic Year:')->assertDontSee('2027');
    }

    public function test_creating_a_new_academic_year_via_quick_setup_does_not_change_the_dashboard(): void
    {
        $admin = $this->makeSchoolAdmin();
        $chosen = AcademicYear::create(['school_id' => $admin->school_id, 'name' => '2025', 'starts_on' => '2025-01-01', 'ends_on' => '2025-12-31']);
        $admin->school->update(['academic_year_id' => $chosen->id]);

        $this->actingAs($admin)->post(route('school.students.initial-setup.store', 'academic-year'), ['name' => '2027']);

        $response = $this->actingAs($admin)->get(route('school.dashboard'));

        $response->assertOk()->assertSee('2025')->assertDontSee('2027');
    }

    public function test_dashboard_reflects_the_year_explicitly_selected_on_institute_update(): void
    {
        $admin = $this->makeSchoolAdmin();
        $year = AcademicYear::create(['school_id' => $admin->school_id, 'name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);

        $this->actingAs($admin)->put(route('school.profile.update'), [
            'name' => $admin->school->name,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->get(route('school.dashboard'));

        $response->assertOk()->assertSee('2026');
    }
}
