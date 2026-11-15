<?php

namespace Tests\Feature\Students;

use App\Models\AcademicYear;
use App\Models\Group;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class StudentImportPreviewTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Student Import Preview Test School',
            'slug' => Str::slug('Student Import Preview Test School').'-'.Str::random(5),
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

    private function makeUploadFile(array $headers, array $row): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($row, null, 'A2');

        $path = tempnam(sys_get_temp_dir(), 'import').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'students.xlsx', null, null, true);
    }

    public function test_roll_number_is_parsed_even_when_the_header_has_a_trailing_period_or_different_case(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $year = AcademicYear::create(['school_id' => $admin->school_id, 'name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);
        $group = Group::create(['school_id' => $admin->school_id, 'name' => 'Science', 'serial' => 1, 'status' => 'enable']);
        $category = StudentCategory::create(['school_id' => $admin->school_id, 'name' => 'Regular', 'serial' => 1, 'status' => 'enable']);

        $file = $this->makeUploadFile(
            ['Roll No.', 'NAME', 'Gender', 'Religion', "Father's Name", "Mother's Name", 'mobile no'],
            ['7', 'Student One', 'Male', 'Islam', 'Father One', 'Mother One', '01700000000']
        );

        $response = $this->actingAs($admin)->post(route('school.students.import.preview', 'without-id'), [
            'academic_year_id' => $year->id,
            'section_id' => $section->id,
            'group_name' => $group->name,
            'category' => $category->name,
            'file' => $file,
        ]);

        $response->assertRedirect(route('school.students.import.create'));

        $preview = session('student_import_preview_without_id');
        $this->assertSame('7', $preview['rows'][0]['roll']);
        $this->assertSame('01700000000', $preview['rows'][0]['guardian_phone']);
    }

    public function test_preview_table_shows_a_serial_number_column(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Nine']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        $year = AcademicYear::create(['school_id' => $admin->school_id, 'name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);
        $group = Group::create(['school_id' => $admin->school_id, 'name' => 'Science', 'serial' => 1, 'status' => 'enable']);
        $category = StudentCategory::create(['school_id' => $admin->school_id, 'name' => 'Regular', 'serial' => 1, 'status' => 'enable']);

        $file = $this->makeUploadFile(
            ['Roll No', 'Name', 'Gender', 'Religion', "Father's Name", "Mother's Name", 'Mobile No.'],
            ['1', 'Student One', 'Male', 'Islam', 'Father One', 'Mother One', '01700000000']
        );

        $this->actingAs($admin)->post(route('school.students.import.preview', 'without-id'), [
            'academic_year_id' => $year->id,
            'section_id' => $section->id,
            'group_name' => $group->name,
            'category' => $category->name,
            'file' => $file,
        ]);

        $response = $this->actingAs($admin)->get(route('school.students.import.create'));

        $response->assertOk()->assertSee('SL');
    }
}
