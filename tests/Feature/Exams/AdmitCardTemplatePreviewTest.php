<?php

namespace Tests\Feature\Exams;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdmitCardTemplatePreviewTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Admit Card Preview Test School',
            'slug' => Str::slug('Admit Card Preview Test School').'-'.Str::random(5),
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

    public function test_template_selection_page_renders_all_four_previews(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->get(route('school.exams.seat-plan', ['tab' => 'template']));

        $response->assertOk()
            ->assertSee('Template 1')
            ->assertSee('Template 2')
            ->assertSee('Template 3')
            ->assertSee('Template 4')
            ->assertSee("Father's Name", false)
            ->assertSee("Mother's Name", false)
            ->assertSee('Head Accountant')
            ->assertSee('Headmaster')
            ->assertSee('Class Teacher')
            ->assertSee('Head Teacher')
            ->assertSee('Exam Routine')
            ->assertSee('Subjects');
    }

    public function test_selecting_a_template_persists_and_marks_it_selected(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->post(route('school.exams.seat-plan.select-template'), ['template' => 3]);

        $response = $this->actingAs($admin)->get(route('school.exams.seat-plan', ['tab' => 'template']));

        $response->assertOk()->assertSee('Selected');
        $this->assertSame(3, $admin->school->fresh()->admit_card_template);
    }
}
