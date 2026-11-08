<?php

namespace Tests\Feature\Exams;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SubjectConfigurationPageTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Subject Config Test School',
            'slug' => Str::slug('Subject Config Test School').'-'.Str::random(5),
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

    public function test_subject_checkboxes_show_before_class_and_group_are_selected(): void
    {
        $admin = $this->makeSchoolAdmin();
        Subject::create(['school_id' => $admin->school_id, 'name' => 'Bangla']);

        $response = $this->actingAs($admin)->get(route('school.exam-configuration.subject'));

        $response->assertOk()->assertSee('Select Subject(s)')->assertSee('Bangla');
    }

    public function test_saving_without_class_and_group_selected_shows_a_validation_error(): void
    {
        $admin = $this->makeSchoolAdmin();
        $subject = Subject::create(['school_id' => $admin->school_id, 'name' => 'Bangla']);

        $response = $this->actingAs($admin)->post(route('school.exam-configuration.subject.store'), [
            'school_class_id' => '',
            'group_name' => '',
            'subject_ids' => [$subject->id],
        ]);

        $response->assertSessionHasErrors(['school_class_id', 'group_name']);
    }

    public function test_saving_with_class_and_group_selected_creates_the_assignment(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $subject = Subject::create(['school_id' => $admin->school_id, 'name' => 'Bangla']);

        $response = $this->actingAs($admin)->post(route('school.exam-configuration.subject.store'), [
            'school_class_id' => $class->id,
            'group_name' => 'Science',
            'subject_ids' => [$subject->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subject_assignments', [
            'school_class_id' => $class->id,
            'group_name' => 'Science',
            'subject_id' => $subject->id,
        ]);
    }
}
