<?php

namespace Tests\Feature\Students;

use App\Models\School;
use App\Models\SchoolClass;
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

    public function test_submitting_a_section_name_creates_it_for_every_class(): void
    {
        $admin = $this->makeSchoolAdmin();
        $classNine = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $classTen = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Ten']);

        $response = $this->actingAs($admin)->post(route('school.students.initial-setup.store', 'section'), [
            'name' => 'A',
            'serial' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sections', ['school_class_id' => $classNine->id, 'name' => 'A']);
        $this->assertDatabaseHas('sections', ['school_class_id' => $classTen->id, 'name' => 'A']);
    }

    public function test_submitting_an_existing_section_name_again_does_not_duplicate_it(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);

        $this->actingAs($admin)->post(route('school.students.initial-setup.store', 'section'), ['name' => 'A']);
        $this->actingAs($admin)->post(route('school.students.initial-setup.store', 'section'), ['name' => 'A']);

        $this->assertSame(1, Section::where('school_class_id', $class->id)->where('name', 'A')->count());
    }
}
