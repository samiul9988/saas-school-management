<?php

namespace Tests\Feature\Students;

use App\Models\Group;
use App\Models\School;
use App\Models\StudentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StudentImportDropdownsTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Student Import Dropdowns Test School',
            'slug' => Str::slug('Student Import Dropdowns Test School').'-'.Str::random(5),
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

    public function test_category_dropdown_lists_the_schools_configured_categories_not_a_fixed_list(): void
    {
        $admin = $this->makeSchoolAdmin();
        StudentCategory::create(['school_id' => $admin->school_id, 'name' => 'Non-Residential', 'serial' => 1, 'status' => 'enable']);
        StudentCategory::create(['school_id' => $admin->school_id, 'name' => 'Disabled Category', 'serial' => 2, 'status' => 'disable']);

        $response = $this->actingAs($admin)->get(route('school.students.import.create'));

        $response->assertOk()
            ->assertSee('Non-Residential')
            ->assertDontSee('Disabled Category')
            ->assertDontSee('Freedom Fighter Quota');
    }

    public function test_group_dropdown_lists_the_schools_configured_groups_not_a_fixed_list(): void
    {
        $admin = $this->makeSchoolAdmin();
        Group::create(['school_id' => $admin->school_id, 'name' => 'Humanities', 'serial' => 1, 'status' => 'enable']);
        Group::create(['school_id' => $admin->school_id, 'name' => 'Disabled Group', 'serial' => 2, 'status' => 'disable']);

        $response = $this->actingAs($admin)->get(route('school.students.import.create'));

        $response->assertOk()
            ->assertSee('Humanities')
            ->assertDontSee('Disabled Group')
            ->assertDontSee('Commerce');
    }
}
