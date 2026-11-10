<?php

namespace Tests\Feature\Exams;

use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExamSettingsSubjectTabTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Exam Settings Subject Tab Test School',
            'slug' => Str::slug('Exam Settings Subject Tab Test School').'-'.Str::random(5),
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

    public function test_subject_tab_lists_subjects_ordered_by_code(): void
    {
        $admin = $this->makeSchoolAdmin();
        Subject::create(['school_id' => $admin->school_id, 'name' => 'Agriculture', 'code' => '8']);
        Subject::create(['school_id' => $admin->school_id, 'name' => 'Bangla 1st Paper', 'code' => '1']);
        Subject::create(['school_id' => $admin->school_id, 'name' => 'Bangla 2nd Paper', 'code' => '2']);

        $response = $this->actingAs($admin)->get(route('school.exams.settings', ['tab' => 'subject']));

        $response->assertOk();
        $content = $response->getContent();
        $banglaFirstPos = strpos($content, 'Bangla 1st Paper');
        $banglaSecondPos = strpos($content, 'Bangla 2nd Paper');
        $agriculturePos = strpos($content, 'Agriculture');

        $this->assertTrue($banglaFirstPos < $banglaSecondPos);
        $this->assertTrue($banglaSecondPos < $agriculturePos);
    }

    public function test_subject_tab_can_delete_a_subject(): void
    {
        $admin = $this->makeSchoolAdmin();
        $subject = Subject::create(['school_id' => $admin->school_id, 'name' => 'Bangla 1st Paper', 'code' => '1']);

        $response = $this->actingAs($admin)->delete(route('school.subjects.destroy', $subject));

        $response->assertRedirect();
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_exams_no_longer_has_a_standalone_sidebar_link_outside_settings(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->get(route('school.dashboard'));

        $response->assertOk();
        // "Settings" (which contains the Exam tab) must still be present...
        $response->assertSee('Settings');
        // ...but the standalone "Exams" link targeting the index route must be gone.
        $response->assertDontSee('href="'.route('school.exams.index').'"', false);
    }
}
