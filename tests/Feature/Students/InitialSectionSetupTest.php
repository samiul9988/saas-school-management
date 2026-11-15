<?php

namespace Tests\Feature\Students;

use App\Models\School;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InitialSectionSetupTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Initial Section Setup Test School',
            'slug' => Str::slug('Initial Section Setup Test School').'-'.Str::random(5),
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

    public function test_section_quick_setup_form_has_no_class_picker(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->get(route('school.students.initial-setup', 'section'));

        $response->assertOk()->assertDontSee('Select Class');
    }

    public function test_period_tab_no_longer_exists(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->get(route('school.students.initial-setup', 'period'));

        $response->assertNotFound();
    }

    public function test_submitting_a_section_name_creates_exactly_one_row_with_no_class(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->post(route('school.students.initial-setup.store', 'section'), [
            'name' => 'B',
            'serial' => 1,
        ]);

        $response->assertRedirect();
        $this->assertSame(1, Section::withoutGlobalScope('assigned')->where('name', 'B')->count());
        $this->assertNull(Section::withoutGlobalScope('assigned')->where('name', 'B')->first()->school_class_id);
    }

    public function test_section_list_does_not_show_a_class_column_since_assignment_happens_in_configuration(): void
    {
        $admin = $this->makeSchoolAdmin();
        Section::create(['school_id' => $admin->school_id, 'name' => 'A']);

        $response = $this->actingAs($admin)->get(route('school.students.initial-setup', 'section'));

        $response->assertOk()->assertDontSee('<th class="py-3 px-4">Class</th>', false)->assertSee('A');
    }
}
