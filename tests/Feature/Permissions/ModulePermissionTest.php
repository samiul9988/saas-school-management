<?php

namespace Tests\Feature\Permissions;

use App\Models\Position;
use App\Models\PositionPermission;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModulePermissionTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Permission Test School',
            'slug' => Str::slug('Permission Test School').'-'.Str::random(5),
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

    private function makeSubUser(User $admin, array $permissions): User
    {
        $position = Position::create(['school_id' => $admin->school_id, 'name' => 'Front Desk']);

        foreach ($permissions as $module => $flags) {
            PositionPermission::create(array_merge(['position_id' => $position->id, 'module' => $module], $flags));
        }

        return User::create([
            'name' => 'Sub User',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => User::ROLE_SCHOOL_OPERATOR,
            'school_id' => $admin->school_id,
            'position_id' => $position->id,
        ]);
    }

    public function test_school_admin_always_has_full_module_access(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->get(route('school.students.index'))->assertOk();
        $this->actingAs($admin)->get(route('school.finance.index'))->assertOk();
    }

    public function test_sub_user_can_view_a_module_their_position_was_granted(): void
    {
        $admin = $this->makeSchoolAdmin();
        $sub = $this->makeSubUser($admin, ['students' => ['can_view' => true]]);

        $this->actingAs($sub)->get(route('school.students.index'))->assertOk();
    }

    public function test_sub_user_is_blocked_from_a_module_their_position_was_not_granted(): void
    {
        $admin = $this->makeSchoolAdmin();
        $sub = $this->makeSubUser($admin, ['students' => ['can_view' => true]]);

        $this->actingAs($sub)->get(route('school.finance.index'))->assertForbidden();
    }

    public function test_sub_user_without_create_permission_cannot_submit_a_create_action(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $sub = $this->makeSubUser($admin, ['academics' => ['can_view' => true, 'can_create' => false]]);

        $this->actingAs($sub)->post(route('school.sections.store'), [
            'school_class_id' => $class->id,
            'name' => 'A',
        ])->assertForbidden();
    }

    public function test_sub_user_with_create_permission_can_submit_a_create_action(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $sub = $this->makeSubUser($admin, ['academics' => ['can_view' => true, 'can_create' => true]]);

        $this->actingAs($sub)->post(route('school.sections.store'), [
            'school_class_id' => $class->id,
            'name' => 'A',
        ])->assertRedirect();

        $this->assertDatabaseHas('sections', ['school_class_id' => $class->id, 'name' => 'A']);
    }

    public function test_sub_user_with_no_position_is_blocked_from_every_mapped_module(): void
    {
        $admin = $this->makeSchoolAdmin();
        $sub = User::create([
            'name' => 'No Position User',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => User::ROLE_SCHOOL_TEACHER_STAFF,
            'school_id' => $admin->school_id,
        ]);

        $this->actingAs($sub)->get(route('school.students.index'))->assertForbidden();
    }

    public function test_sidebar_only_lists_modules_the_sub_user_can_view(): void
    {
        $admin = $this->makeSchoolAdmin();
        $sub = $this->makeSubUser($admin, ['students' => ['can_view' => true]]);

        $response = $this->actingAs($sub)->get(route('school.dashboard'));

        $response->assertOk()->assertSee('Students')->assertDontSee('Finance');
    }
}
