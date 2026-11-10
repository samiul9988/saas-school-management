<?php

namespace Tests\Feature\Exams;

use App\Models\ExamShortCode;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExamShortCodeDefaultsTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Short Code Defaults Test School',
            'slug' => Str::slug('Short Code Defaults Test School').'-'.Str::random(5),
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

    public function test_short_code_can_be_created_with_note_and_default_marks(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->post(route('school.exam-short-codes.store'), [
            'name' => 'SC-1',
            'note' => 'Short Code -1',
            'default_total' => 50,
            'default_pass_mark' => 17,
            'default_acceptance' => 1,
        ]);

        $response->assertRedirect();

        $shortCode = ExamShortCode::where('name', 'SC-1')->first();
        $this->assertSame('Short Code -1', $shortCode->note);
        $this->assertEquals(50, $shortCode->default_total);
        $this->assertEquals(17, $shortCode->default_pass_mark);
        $this->assertEquals(1, $shortCode->default_acceptance);
    }

    public function test_short_code_tab_lists_note_and_default_marks_columns(): void
    {
        $admin = $this->makeSchoolAdmin();
        ExamShortCode::create([
            'school_id' => $admin->school_id,
            'name' => 'SC-1',
            'note' => 'Short Code -1',
            'serial' => 1,
            'default_total' => 50,
            'default_pass_mark' => 17,
            'default_acceptance' => 1,
        ]);

        $response = $this->actingAs($admin)->get(route('school.exams.settings', ['tab' => 'short-code']));

        $response->assertOk()->assertSee('Short Code -1')->assertSee('50');
    }

    public function test_mark_configuration_prefills_from_the_short_codes_own_defaults(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'S1', 'roll' => '1', 'status' => 'active', 'group_name' => 'Science']);
        Subject::create(['school_id' => $admin->school_id, 'name' => 'Physics']);
        ExamShortCode::create([
            'school_id' => $admin->school_id,
            'name' => 'SC-1',
            'serial' => 1,
            'default_total' => 50,
            'default_pass_mark' => 17,
            'default_acceptance' => 1,
        ]);

        $response = $this->actingAs($admin)->get(route('school.exam-configuration.mark', [
            'school_class_id' => $class->id,
            'group_name' => 'Science',
        ]));

        $response->assertOk()->assertSee('value="50"', false)->assertSee('value="17"', false);
    }
}
