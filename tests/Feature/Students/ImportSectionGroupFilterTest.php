<?php

namespace Tests\Feature\Students;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImportSectionGroupFilterTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Import Section Group Filter Test School',
            'slug' => Str::slug('Import Section Group Filter Test School').'-'.Str::random(5),
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

    public function test_import_page_tags_each_section_option_with_its_class_and_wires_the_admission_groups_endpoint(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'B']);

        $response = $this->actingAs($admin)->get(route('school.students.import.create'));

        $response->assertOk()
            ->assertSee('data-class-id="'.$class->id.'"', false)
            ->assertSee("'admission-classes', null, 'classId'", false);
    }
}
