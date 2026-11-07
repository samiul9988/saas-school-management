<?php

namespace Tests\Feature\Exams;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DynamicGroupOptionsTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Dynamic Group Test School',
            'slug' => Str::slug('Dynamic Group Test School').'-'.Str::random(5),
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

    public function test_section_groups_endpoint_returns_only_groups_actually_used_in_that_section(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $sectionA = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $sectionB = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'B']);

        Student::create(['school_id' => $admin->school_id, 'section_id' => $sectionA->id, 'school_class_id' => $class->id, 'name' => 'S1', 'roll' => '1', 'status' => 'active', 'group_name' => 'Generel']);
        Student::create(['school_id' => $admin->school_id, 'section_id' => $sectionB->id, 'school_class_id' => $class->id, 'name' => 'S2', 'roll' => '1', 'status' => 'active', 'group_name' => 'SCIENCE']);
        Student::create(['school_id' => $admin->school_id, 'section_id' => $sectionB->id, 'school_class_id' => $class->id, 'name' => 'S3', 'roll' => '2', 'status' => 'active', 'group_name' => 'Business']);

        $responseA = $this->actingAs($admin)->getJson(route('school.sections.groups', $sectionA));
        $responseA->assertOk()->assertExactJson(['groups' => ['Generel']]);

        $responseB = $this->actingAs($admin)->getJson(route('school.sections.groups', $sectionB));
        $responseB->assertOk();
        $this->assertEqualsCanonicalizing(['Business', 'SCIENCE'], $responseB->json('groups'));
    }

    public function test_class_groups_endpoint_returns_only_groups_actually_used_in_that_class(): void
    {
        $admin = $this->makeSchoolAdmin();
        $classA = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $classB = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Ten']);

        Student::create(['school_id' => $admin->school_id, 'school_class_id' => $classA->id, 'name' => 'S1', 'roll' => '1', 'status' => 'active', 'group_name' => 'Science']);
        Student::create(['school_id' => $admin->school_id, 'school_class_id' => $classB->id, 'name' => 'S2', 'roll' => '1', 'status' => 'active', 'group_name' => 'Arts']);

        $response = $this->actingAs($admin)->getJson(route('school.classes.groups', $classA));

        $response->assertOk()->assertExactJson(['groups' => ['Science']]);
    }

    public function test_mark_input_page_lists_only_groups_present_in_the_selected_section(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);

        Student::create(['school_id' => $admin->school_id, 'section_id' => $section->id, 'school_class_id' => $class->id, 'name' => 'S1', 'roll' => '1', 'status' => 'active', 'group_name' => 'Generel']);

        $response = $this->actingAs($admin)->get(route('school.mark-input.index', ['section_id' => $section->id]));

        $response->assertOk()->assertSee('Generel')->assertDontSee('Commerce');
    }
}
