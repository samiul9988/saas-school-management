<?php

namespace Tests\Feature\Students;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClassConfigurationListTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Class Configuration List Test School',
            'slug' => Str::slug('Class Configuration List Test School').'-'.Str::random(5),
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

    public function test_a_section_created_elsewhere_does_not_appear_until_explicitly_configured_here(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);

        $response = $this->actingAs($admin)->get(route('school.students.configuration.class'));

        $response->assertOk()->assertDontSee('No section records yet.')->assertDontSee('>A<', false);
        $this->assertNull($section->fresh()->shift_id);
    }

    public function test_saving_a_shift_for_a_section_makes_it_appear_in_the_list(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $shift = Shift::create(['school_id' => $admin->school_id, 'name' => 'Morning', 'serial' => 1]);

        $this->actingAs($admin)->post(route('school.students.configuration.class.store'), [
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'shift_id' => $shift->id,
        ]);

        $response = $this->actingAs($admin)->get(route('school.students.configuration.class'));

        $response->assertOk()->assertSee('Morning');
    }
}
