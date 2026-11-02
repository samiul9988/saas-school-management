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

class AcademicInfoBulkUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Academic Bulk Update Test School',
            'slug' => Str::slug('Academic Bulk Update Test School').'-'.Str::random(5),
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

    public function test_multiple_students_can_be_moved_to_a_new_section_in_one_request(): void
    {
        $admin = $this->makeSchoolAdmin();
        $classA = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 9']);
        $classB = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 10']);
        $sectionA = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $classA->id, 'name' => 'A']);
        $sectionB = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $classB->id, 'name' => 'B']);

        $student1 = Student::create(['school_id' => $admin->school_id, 'section_id' => $sectionA->id, 'school_class_id' => $classA->id, 'name' => 'Student One', 'roll' => '1', 'status' => 'active']);
        $student2 = Student::create(['school_id' => $admin->school_id, 'section_id' => $sectionA->id, 'school_class_id' => $classA->id, 'name' => 'Student Two', 'roll' => '2', 'status' => 'active']);

        $this->actingAs($admin)->post(route('school.students.update-academic.store'), [
            'section_id' => $sectionA->id,
            'student_ids' => [$student1->id, $student2->id],
            'school_class_id' => $classB->id,
            'new_section_id' => $sectionB->id,
            'group_name' => 'Science',
        ]);

        $this->assertSame($sectionB->id, $student1->fresh()->section_id);
        $this->assertSame($sectionB->id, $student2->fresh()->section_id);
        $this->assertSame('Science', $student1->fresh()->group_name);
    }

    public function test_roll_is_only_applied_when_a_single_student_is_selected(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 9']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);

        $student1 = Student::create(['school_id' => $admin->school_id, 'section_id' => $section->id, 'school_class_id' => $class->id, 'name' => 'Student One', 'roll' => '1', 'status' => 'active']);
        $student2 = Student::create(['school_id' => $admin->school_id, 'section_id' => $section->id, 'school_class_id' => $class->id, 'name' => 'Student Two', 'roll' => '2', 'status' => 'active']);

        $this->actingAs($admin)->post(route('school.students.update-academic.store'), [
            'section_id' => $section->id,
            'student_ids' => [$student1->id, $student2->id],
            'school_class_id' => $class->id,
            'new_section_id' => $section->id,
            'roll' => '99',
        ]);

        $this->assertSame('1', $student1->fresh()->roll);
        $this->assertSame('2', $student2->fresh()->roll);
    }
}
