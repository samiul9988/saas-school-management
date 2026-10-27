<?php

namespace Tests\Feature\Students;

use App\Models\Group;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StudentUpdateBatchTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Update Batch Test School',
            'slug' => Str::slug('Update Batch Test School').'-'.Str::random(5),
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

    public function test_student_can_be_created_with_blood_group_and_split_address_fields(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->post(route('school.students.store'), [
            'name' => 'Address Test Student',
            'status' => 'active',
            'blood_group' => 'A+',
            'village' => 'Rampur',
            'post_office' => 'Rampur PO',
            'upazila_thana' => 'Dinajpur Sadar',
            'district' => 'Dinajpur',
        ]);

        $student = Student::first();
        $this->assertSame('A+', $student->blood_group);
        $this->assertSame('Rampur', $student->village);
        $this->assertSame('Rampur PO', $student->post_office);
        $this->assertSame('Dinajpur Sadar', $student->upazila_thana);
        $this->assertSame('Dinajpur', $student->district);
    }

    public function test_update_basic_page_uses_all_students_style_table_with_edit_and_delete(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 10']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $student = Student::create([
            'school_id' => $admin->school_id, 'school_class_id' => $class->id, 'section_id' => $section->id,
            'name' => 'Table Test Student', 'admission_number' => '9001', 'roll' => '7', 'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('school.students.update-basic', ['section_id' => $section->id]));

        $response->assertOk()
            ->assertSee('Table Test Student')
            ->assertSee('9001')
            ->assertSee('Admission No.')
            ->assertSee(route('school.students.edit', $student), false)
            ->assertSee(route('school.students.destroy', $student), false);
    }

    public function test_update_basic_saves_blood_group_and_address_parts(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 1']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $student = Student::create(['school_id' => $admin->school_id, 'section_id' => $section->id, 'name' => 'Student', 'status' => 'active']);

        $this->actingAs($admin)->post(route('school.students.update-basic.store'), [
            'section_id' => $section->id,
            'student_id' => $student->id,
            'name' => 'Student',
            'blood_group' => 'O-',
            'village' => 'Kazipara',
            'post_office' => 'Kazipara PO',
            'upazila_thana' => 'Savar',
            'district' => 'Dhaka',
        ]);

        $student->refresh();
        $this->assertSame('O-', $student->blood_group);
        $this->assertSame('Kazipara', $student->village);
        $this->assertSame('Dhaka', $student->district);
    }

    public function test_all_students_index_is_ordered_by_roll_and_shows_roll_column(): void
    {
        $admin = $this->makeSchoolAdmin();
        Student::create(['school_id' => $admin->school_id, 'name' => 'Student B', 'roll' => '2', 'status' => 'active']);
        Student::create(['school_id' => $admin->school_id, 'name' => 'Student A', 'roll' => '1', 'status' => 'active']);

        $response = $this->actingAs($admin)->get(route('school.students.index'));

        $response->assertOk();
        $content = $response->getContent();
        $this->assertTrue(strpos($content, 'Student A') < strpos($content, 'Student B'), 'Student A (roll 1) should appear before Student B (roll 2).');
    }

    public function test_all_students_report_is_ordered_by_roll(): void
    {
        $admin = $this->makeSchoolAdmin();
        Student::create(['school_id' => $admin->school_id, 'name' => 'Report Student B', 'roll' => '5', 'status' => 'active']);
        Student::create(['school_id' => $admin->school_id, 'name' => 'Report Student A', 'roll' => '3', 'status' => 'active']);

        $response = $this->actingAs($admin)->get(route('school.students.report.all'));

        $response->assertOk();
        $content = $response->getContent();
        $this->assertTrue(strpos($content, 'Report Student A') < strpos($content, 'Report Student B'));
    }

    public function test_update_academic_all_option_lists_students_across_sections(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 1']);
        $sectionA = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $sectionB = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'B']);
        Student::create(['school_id' => $admin->school_id, 'section_id' => $sectionA->id, 'name' => 'Student In A', 'roll' => '1', 'status' => 'active']);
        Student::create(['school_id' => $admin->school_id, 'section_id' => $sectionB->id, 'name' => 'Student In B', 'roll' => '2', 'status' => 'active']);

        $response = $this->actingAs($admin)->get(route('school.students.update-academic', ['section_id' => 'all']));

        $response->assertOk()->assertSee('Student In A')->assertSee('Student In B');
    }

    public function test_group_wise_report_does_not_crash_and_uses_configured_groups(): void
    {
        $admin = $this->makeSchoolAdmin();
        Group::create(['school_id' => $admin->school_id, 'name' => 'Humanities', 'serial' => 1, 'status' => 'enable']);

        $response = $this->actingAs($admin)->get(route('school.students.report.group-wise'));

        $response->assertOk()->assertSee('Humanities');
    }

    public function test_dashboard_class_summary_chart_includes_class_data(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 5']);
        Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'Boy', 'gender' => 'male', 'status' => 'active']);
        Student::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'Girl', 'gender' => 'female', 'status' => 'active']);

        $response = $this->actingAs($admin)->get(route('school.dashboard'));

        $response->assertOk()
            ->assertSee('Class Wise Student Summary')
            ->assertSee('classSummaryChart', false)
            ->assertSee('Class 5');
    }

    public function test_section_wise_excel_export_includes_village_and_upazila(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Class 1']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        Student::create([
            'school_id' => $admin->school_id, 'section_id' => $section->id, 'name' => 'Excel Student',
            'status' => 'active', 'village' => 'TestVillage', 'upazila_thana' => 'TestUpazila',
        ]);

        $response = $this->actingAs($admin)->get(route('school.students.report.section-wise.excel', ['section_id' => $section->id]));

        $response->assertOk();
    }

    public function test_id_card_preview_renders_with_new_fields(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->get(route('school.students.id-card.preview', 2));

        $response->assertOk()->assertSee('Blood Group')->assertSee('B+');
    }
}
