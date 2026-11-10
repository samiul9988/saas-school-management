<?php

namespace Tests\Feature\Exams;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Grade;
use App\Models\Mark;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Support\ResultCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClassWiseGradeSetupTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Class Wise Grade Test School',
            'slug' => Str::slug('Class Wise Grade Test School').'-'.Str::random(5),
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

    public function test_checking_standard_grades_creates_them_for_the_selected_class(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);

        $response = $this->actingAs($admin)->post(route('school.grades.class-setup'), [
            'school_class_id' => $class->id,
            'grades' => ['A+', 'A', 'F'],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('grades', ['school_class_id' => $class->id, 'name' => 'A+', 'min_percent' => 80, 'max_percent' => 100, 'grade_point' => 5]);
        $this->assertDatabaseHas('grades', ['school_class_id' => $class->id, 'name' => 'A', 'min_percent' => 70, 'max_percent' => 79, 'grade_point' => 4]);
        $this->assertDatabaseHas('grades', ['school_class_id' => $class->id, 'name' => 'F']);
        $this->assertDatabaseMissing('grades', ['school_class_id' => $class->id, 'name' => 'B']);
    }

    public function test_unchecking_a_previously_selected_grade_removes_it_for_that_class(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);

        $this->actingAs($admin)->post(route('school.grades.class-setup'), [
            'school_class_id' => $class->id,
            'grades' => ['A+', 'A'],
        ]);

        $this->actingAs($admin)->post(route('school.grades.class-setup'), [
            'school_class_id' => $class->id,
            'grades' => ['A+'],
        ]);

        $this->assertDatabaseHas('grades', ['school_class_id' => $class->id, 'name' => 'A+']);
        $this->assertDatabaseMissing('grades', ['school_class_id' => $class->id, 'name' => 'A']);
    }

    public function test_grade_setup_for_one_class_does_not_affect_another_class(): void
    {
        $admin = $this->makeSchoolAdmin();
        $classNine = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $classTen = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Ten']);

        $this->actingAs($admin)->post(route('school.grades.class-setup'), [
            'school_class_id' => $classNine->id,
            'grades' => ['A+'],
        ]);

        $this->assertDatabaseMissing('grades', ['school_class_id' => $classTen->id, 'name' => 'A+']);
    }

    public function test_result_calculator_uses_the_students_class_specific_grade_scale(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $subject = Subject::create(['school_id' => $admin->school_id, 'name' => 'Bangla']);
        $exam = Exam::create(['school_id' => $admin->school_id, 'name' => 'Half Yearly', 'status' => 'ongoing']);
        $examSubject = ExamSubject::create([
            'school_id' => $admin->school_id,
            'exam_id' => $exam->id,
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'full_marks' => 100,
            'pass_marks' => 33,
        ]);
        $student = Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'Student One', 'roll' => '1', 'status' => 'active']);
        Mark::create(['school_id' => $admin->school_id, 'exam_subject_id' => $examSubject->id, 'student_id' => $student->id, 'marks_obtained' => 85]);

        // A custom, class-specific scale where 85% is labelled "Outstanding" instead of the global "A+".
        Grade::create(['school_id' => $admin->school_id, 'name' => 'Outstanding', 'min_percent' => 80, 'max_percent' => 100, 'grade_point' => 5]);
        Grade::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'Custom Top', 'min_percent' => 80, 'max_percent' => 100, 'grade_point' => 5]);

        $results = ResultCalculator::forExam($exam, $class->id, collect([$student]));

        $this->assertSame('Custom Top', $results[$student->id]['grade']->name);
    }
}
