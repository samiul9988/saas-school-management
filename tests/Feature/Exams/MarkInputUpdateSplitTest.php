<?php

namespace Tests\Feature\Exams;

use App\Models\Exam;
use App\Models\ExamShortCode;
use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarkInputUpdateSplitTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Mark Split Test School',
            'slug' => Str::slug('Mark Split Test School').'-'.Str::random(5),
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

    private function setUpExamSubject(User $admin): array
    {
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'Bangla']);
        $exam = Exam::create(['school_id' => $admin->school_id, 'name' => 'Half Yearly', 'status' => 'ongoing']);
        $examSubject = ExamSubject::create([
            'school_id' => $admin->school_id,
            'exam_id' => $exam->id,
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'full_marks' => 100,
            'pass_marks' => 33,
        ]);
        $student = Student::create(['school_id' => $admin->school_id, 'section_id' => $section->id, 'school_class_id' => $class->id, 'name' => 'Student One', 'roll' => '1', 'status' => 'active']);

        $shortCode = ExamShortCode::create(['school_id' => $admin->school_id, 'name' => 'WR', 'serial' => 1]);
        SubjectComponent::create(['school_id' => $admin->school_id, 'subject_id' => $subject->id, 'exam_short_code_id' => $shortCode->id, 'total' => 50, 'pass_mark' => 0, 'acceptance' => 1, 'serial' => 1]);

        return compact('class', 'section', 'subject', 'exam', 'examSubject', 'student');
    }

    public function test_a_subject_with_no_marks_yet_is_only_available_from_mark_input(): void
    {
        $admin = $this->makeSchoolAdmin();
        ['section' => $section, 'subject' => $subject, 'exam' => $exam] = $this->setUpExamSubject($admin);

        $query = ['section_id' => $section->id, 'exam_id' => $exam->id, 'subject_id' => $subject->id];

        $input = $this->actingAs($admin)->get(route('school.mark-input.index', $query));
        $input->assertOk()->assertSee('name="marks[', false);

        $update = $this->actingAs($admin)->get(route('school.mark-update.index', $query));
        $update->assertOk()
            ->assertSee('No marks have been entered for this subject yet. Please use Mark Input first.')
            ->assertDontSee('name="marks[', false);
    }

    public function test_a_subject_with_marks_already_entered_is_only_available_from_mark_update(): void
    {
        $admin = $this->makeSchoolAdmin();
        ['section' => $section, 'subject' => $subject, 'exam' => $exam, 'examSubject' => $examSubject, 'student' => $student] = $this->setUpExamSubject($admin);

        Mark::create(['school_id' => $admin->school_id, 'exam_subject_id' => $examSubject->id, 'student_id' => $student->id, 'marks_obtained' => 80]);

        $query = ['section_id' => $section->id, 'exam_id' => $exam->id, 'subject_id' => $subject->id];

        $update = $this->actingAs($admin)->get(route('school.mark-update.index', $query));
        $update->assertOk()->assertSee('name="marks[', false);

        $input = $this->actingAs($admin)->get(route('school.mark-input.index', $query));
        $input->assertOk()
            ->assertSee('Marks for this subject have already been entered. Please use Mark Update to edit them.')
            ->assertDontSee('name="marks[', false);
    }

    public function test_subjects_dropdown_endpoint_filters_by_mode(): void
    {
        $admin = $this->makeSchoolAdmin();
        ['section' => $section, 'subject' => $subject, 'exam' => $exam, 'examSubject' => $examSubject, 'student' => $student] = $this->setUpExamSubject($admin);

        $inputBefore = $this->actingAs($admin)->getJson(route('school.mark-input.subjects', ['section_id' => $section->id, 'exam_id' => $exam->id, 'mode' => 'input']));
        $inputBefore->assertOk()->assertJsonFragment(['id' => $subject->id]);

        $updateBefore = $this->actingAs($admin)->getJson(route('school.mark-input.subjects', ['section_id' => $section->id, 'exam_id' => $exam->id, 'mode' => 'update']));
        $updateBefore->assertOk()->assertJsonMissing(['id' => $subject->id]);

        Mark::create(['school_id' => $admin->school_id, 'exam_subject_id' => $examSubject->id, 'student_id' => $student->id, 'marks_obtained' => 80]);

        $inputAfter = $this->actingAs($admin)->getJson(route('school.mark-input.subjects', ['section_id' => $section->id, 'exam_id' => $exam->id, 'mode' => 'input']));
        $inputAfter->assertOk()->assertJsonMissing(['id' => $subject->id]);

        $updateAfter = $this->actingAs($admin)->getJson(route('school.mark-input.subjects', ['section_id' => $section->id, 'exam_id' => $exam->id, 'mode' => 'update']));
        $updateAfter->assertOk()->assertJsonFragment(['id' => $subject->id]);
    }
}
