<?php

namespace Tests\Feature\Students;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class IdCardDownloadTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Id Card Download Test School',
            'slug' => Str::slug('Id Card Download Test School').'-'.Str::random(5),
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

    public function test_downloading_front_for_a_section_with_no_students_shows_an_error_instead_of_a_blank_pdf(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);

        $response = $this->actingAs($admin)->get(route('school.students.id-card.front', ['section_id' => $section->id]));

        $response->assertRedirect(route('school.students.id-card.index'));
        $this->assertStringContainsString('No students found', session('error'));
    }

    public function test_downloading_back_for_a_section_with_no_students_shows_an_error_instead_of_a_blank_pdf(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);

        $response = $this->actingAs($admin)->get(route('school.students.id-card.back', ['section_id' => $section->id]));

        $response->assertRedirect(route('school.students.id-card.index'));
        $this->assertStringContainsString('No students found', session('error'));
    }

    public function test_downloading_front_for_a_section_with_students_returns_a_pdf(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        Student::create(['school_id' => $admin->school_id, 'section_id' => $section->id, 'school_class_id' => $class->id, 'name' => 'Student One', 'roll' => '1', 'status' => 'active']);

        $response = $this->actingAs($admin)->get(route('school.students.id-card.front', ['section_id' => $section->id]));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }
}
