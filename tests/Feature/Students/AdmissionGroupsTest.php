<?php

namespace Tests\Feature\Students;

use App\Models\ClassGroupAssignment;
use App\Models\Group;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdmissionGroupsTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Admission Groups Test School',
            'slug' => Str::slug('Admission Groups Test School').'-'.Str::random(5),
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

    public function test_admission_groups_endpoint_only_returns_groups_configured_for_that_class(): void
    {
        $admin = $this->makeSchoolAdmin();
        $classSix = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Six']);
        $classSeven = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Seven']);
        $science = Group::create(['school_id' => $admin->school_id, 'name' => 'Science', 'serial' => 1, 'status' => 'enable']);
        $commerce = Group::create(['school_id' => $admin->school_id, 'name' => 'Commerce', 'serial' => 2, 'status' => 'enable']);

        ClassGroupAssignment::create(['school_id' => $admin->school_id, 'school_class_id' => $classSix->id, 'group_id' => $science->id, 'status' => 'enable']);
        ClassGroupAssignment::create(['school_id' => $admin->school_id, 'school_class_id' => $classSeven->id, 'group_id' => $commerce->id, 'status' => 'enable']);

        $response = $this->actingAs($admin)->getJson(route('school.classes.admission-groups', $classSix));

        $response->assertOk()->assertJson(['groups' => ['Science']]);
    }

    public function test_admission_groups_endpoint_falls_back_to_all_configured_groups_when_class_has_no_assignment(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Six']);
        Group::create(['school_id' => $admin->school_id, 'name' => 'Science', 'serial' => 1, 'status' => 'enable']);
        Group::create(['school_id' => $admin->school_id, 'name' => 'Commerce', 'serial' => 2, 'status' => 'enable']);

        $response = $this->actingAs($admin)->getJson(route('school.classes.admission-groups', $class));

        $response->assertOk();
        $this->assertEqualsCanonicalizing(['Science', 'Commerce'], $response->json('groups'));
    }

    public function test_add_student_page_wires_the_admission_groups_endpoint(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->get(route('school.students.create'));

        $response->assertOk()->assertSee("'admission-classes'", false);
    }
}
