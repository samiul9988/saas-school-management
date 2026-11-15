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

    public function test_a_section_name_created_in_settings_does_not_appear_until_assigned_to_a_class_here(): void
    {
        $admin = $this->makeSchoolAdmin();
        Section::create(['school_id' => $admin->school_id, 'name' => 'A']);

        $response = $this->actingAs($admin)->get(route('school.students.configuration.class'));

        $response->assertOk()->assertDontSee('<td class="py-2 px-4">A</td>', false);
    }

    public function test_assigning_a_section_name_to_a_class_here_makes_it_appear_in_the_list(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        Section::create(['school_id' => $admin->school_id, 'name' => 'A']);
        $shift = Shift::create(['school_id' => $admin->school_id, 'name' => 'Morning', 'serial' => 1]);

        $this->actingAs($admin)->post(route('school.students.configuration.class.store'), [
            'school_class_id' => $class->id,
            'section_name' => 'A',
            'shift_id' => $shift->id,
        ]);

        $response = $this->actingAs($admin)->get(route('school.students.configuration.class'));

        $response->assertOk()->assertSee('Morning');
        $this->assertDatabaseHas('sections', ['school_class_id' => $class->id, 'name' => 'A', 'shift_id' => $shift->id]);
    }

    public function test_the_same_section_name_can_be_assigned_to_multiple_classes(): void
    {
        $admin = $this->makeSchoolAdmin();
        $classNine = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $classTen = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Ten']);
        Section::create(['school_id' => $admin->school_id, 'name' => 'A']);

        $this->actingAs($admin)->post(route('school.students.configuration.class.store'), [
            'school_class_id' => $classNine->id,
            'section_name' => 'A',
        ]);
        $this->actingAs($admin)->post(route('school.students.configuration.class.store'), [
            'school_class_id' => $classTen->id,
            'section_name' => 'A',
        ]);

        $this->assertDatabaseHas('sections', ['school_class_id' => $classNine->id, 'name' => 'A']);
        $this->assertDatabaseHas('sections', ['school_class_id' => $classTen->id, 'name' => 'A']);
    }
}
