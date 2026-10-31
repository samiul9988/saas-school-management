<?php

namespace Tests\Feature\Exams;

use App\Models\Exam;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdmitCardDownloadTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Admit Card Download Test School',
            'slug' => Str::slug('Admit Card Download Test School').'-'.Str::random(5),
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

    public function test_admit_card_pdf_downloads_for_each_template(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 10']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $exam = Exam::create(['school_id' => $admin->school_id, 'name' => 'Annual Exam', 'status' => 'completed']);

        foreach (range(1, 4) as $roll) {
            Student::create([
                'school_id' => $admin->school_id,
                'section_id' => $section->id,
                'school_class_id' => $class->id,
                'admission_number' => 'STU-'.$roll,
                'name' => 'Student '.$roll,
                'roll' => (string) $roll,
                'status' => 'active',
            ]);
        }

        foreach (range(1, 4) as $template) {
            $admin->school->update(['admit_card_template' => $template]);

            $response = $this->actingAs($admin)->get(route('school.exams.seat-plan.admit-card', [
                'section_id' => $section->id,
                'exam_id' => $exam->id,
            ]));

            $response->assertOk();
            $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        }
    }

    public function test_seat_plan_pdf_downloads_ten_and_four_per_page(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 10']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $exam = Exam::create(['school_id' => $admin->school_id, 'name' => 'Annual Exam', 'status' => 'completed']);

        Student::create([
            'school_id' => $admin->school_id,
            'section_id' => $section->id,
            'school_class_id' => $class->id,
            'admission_number' => 'STU-1',
            'name' => 'Student One',
            'roll' => '1',
            'status' => 'active',
        ]);

        foreach ([10, 4] as $perPage) {
            $response = $this->actingAs($admin)->get(route('school.exams.seat-plan.seats', [
                'perPage' => $perPage,
                'section_id' => $section->id,
                'exam_id' => $exam->id,
            ]));

            $response->assertOk();
            $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        }
    }
}
