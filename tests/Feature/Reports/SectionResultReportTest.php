<?php

namespace Tests\Feature\Reports;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\ResultBatch;
use App\Models\ResultBatchResult;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SectionResultReportTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Section Result Test School',
            'slug' => Str::slug('Section Result Test School').'-'.Str::random(5),
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

    private function setUpProcessedResult(User $admin): array
    {
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 10']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $exam = Exam::create(['school_id' => $admin->school_id, 'name' => 'Half Yearly', 'status' => 'completed']);
        $grade = Grade::create(['school_id' => $admin->school_id, 'name' => 'A+', 'min_percent' => 80, 'max_percent' => 100, 'grade_point' => 5]);

        $student = Student::create([
            'school_id' => $admin->school_id,
            'section_id' => $section->id,
            'school_class_id' => $class->id,
            'admission_number' => 'STU-1001',
            'name' => 'Roll One',
            'roll' => '1',
            'status' => 'active',
        ]);

        $resultBatch = ResultBatch::create([
            'school_id' => $admin->school_id,
            'type' => ResultBatch::TYPE_EXAM,
            'exam_id' => $exam->id,
            'name' => $exam->name,
        ]);

        $result = ResultBatchResult::create([
            'school_id' => $admin->school_id,
            'result_batch_id' => $resultBatch->id,
            'student_id' => $student->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'total_obtained' => 450,
            'total_full' => 500,
            'percentage' => 90,
            'gpa' => 5,
            'grade_id' => $grade->id,
            'position' => 1,
            'is_passed' => true,
            'failed_subjects_count' => 0,
        ]);

        return compact('exam', 'section', 'student', 'result');
    }

    public function test_section_result_report_shows_all_required_columns(): void
    {
        $admin = $this->makeSchoolAdmin();
        ['exam' => $exam, 'section' => $section] = $this->setUpProcessedResult($admin);

        $response = $this->actingAs($admin)->get(route('school.reports.section-result', [
            'exam_id' => $exam->id,
            'section_id' => $section->id,
        ]));

        $response->assertOk()
            ->assertSee('ID No')
            ->assertSee('Roll')
            ->assertSee('Name')
            ->assertSee('Percentage')
            ->assertSee('GPA')
            ->assertSee('Grade')
            ->assertSee('Status')
            ->assertSee('Total Failed Subject')
            ->assertSee('STU-1001')
            ->assertSee('Download PDF')
            ->assertSee('Download Excel');
    }

    public function test_section_result_pdf_download_returns_pdf(): void
    {
        $admin = $this->makeSchoolAdmin();
        ['exam' => $exam, 'section' => $section] = $this->setUpProcessedResult($admin);

        $response = $this->actingAs($admin)->get(route('school.reports.section-result.pdf', [
            'exam_id' => $exam->id,
            'section_id' => $section->id,
        ]));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_section_result_excel_download_returns_spreadsheet(): void
    {
        $admin = $this->makeSchoolAdmin();
        ['exam' => $exam, 'section' => $section] = $this->setUpProcessedResult($admin);

        $response = $this->actingAs($admin)->get(route('school.reports.section-result.excel', [
            'exam_id' => $exam->id,
            'section_id' => $section->id,
        ]));

        $response->assertOk();
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );
    }
}
