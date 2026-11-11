<?php

namespace Tests\Feature\Students;

use App\Models\School;
use App\Models\StudentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AddStudentFormDropdownsTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Add Student Form Dropdowns Test School',
            'slug' => Str::slug('Add Student Form Dropdowns Test School').'-'.Str::random(5),
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

        $response = $this->actingAs($admin)->get(route('school.students.create'));

        $response->assertOk()
            ->assertSee('Non-Residential')
            ->assertDontSee('Disabled Category')
            ->assertDontSee('Freedom Fighter Quota');
    }

    public function test_category_dropdown_falls_back_to_defaults_when_none_configured(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->get(route('school.students.create'));

        $response->assertOk()->assertSee('Freedom Fighter Quota');
    }
}
