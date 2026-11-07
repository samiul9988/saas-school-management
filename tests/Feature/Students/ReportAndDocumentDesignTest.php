<?php

namespace Tests\Feature\Students;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Signature;
use App\Models\Student;
use App\Models\Testimonial;
use App\Models\TransferCertificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ReportAndDocumentDesignTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Document Design Test School',
            'slug' => Str::slug('Document Design Test School').'-'.Str::random(5),
            'email' => fake()->unique()->safeEmail(),
            'address' => 'Birganj, Dinajpur',
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

    public function test_section_wise_excel_includes_school_and_class_header_rows(): void
    {
        $admin = $this->makeSchoolAdmin();
        $class = SchoolClass::create(['school_id' => $admin->school_id, 'name' => 'Six']);
        $section = Section::create(['school_id' => $admin->school_id, 'school_class_id' => $class->id, 'name' => 'A']);
        Student::create(['school_id' => $admin->school_id, 'section_id' => $section->id, 'school_class_id' => $class->id, 'name' => 'Faruk Islam', 'roll' => '1', 'status' => 'active']);

        $response = $this->actingAs($admin)->get(route('school.students.report.section-wise.excel', ['section_id' => $section->id]));

        $response->assertOk();

        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
        file_put_contents($tmpFile, $response->streamedContent());

        $sheet = IOFactory::load($tmpFile)->getActiveSheet();

        $this->assertSame('Document Design Test School', $sheet->getCell('A1')->getValue());
        $this->assertSame('Birganj, Dinajpur', $sheet->getCell('A2')->getValue());
        $this->assertSame('Student Report (Section Wise)', $sheet->getCell('A3')->getValue());
        $this->assertSame('Class: Six, Section: A', $sheet->getCell('A4')->getValue());
        $this->assertSame('ID', $sheet->getCell('A5')->getValue());
        $this->assertSame('Faruk Islam', $sheet->getCell('B6')->getValue());

        unlink($tmpFile);
    }

    public function test_testimonial_pdf_downloads_with_all_academic_fields(): void
    {
        $admin = $this->makeSchoolAdmin();
        $student = Student::create(['school_id' => $admin->school_id, 'name' => 'Faruk Islam', 'roll' => '1', 'status' => 'active']);

        $testimonial = Testimonial::create([
            'school_id' => $admin->school_id,
            'serial' => (string) Str::uuid(),
            'student_id' => $student->id,
            'student_name' => $student->name,
            'class_name' => 'Six',
            'roll' => '1',
            'gpa' => '5.00',
            'registration_number' => 'REG-1',
            'session' => '2026',
            'date_of_birth' => '2015-01-01',
            'issue_date' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('school.students.testimonial.download', $testimonial));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_transfer_certificate_pdf_downloads(): void
    {
        $admin = $this->makeSchoolAdmin();
        $student = Student::create(['school_id' => $admin->school_id, 'name' => 'Faruk Islam', 'roll' => '1', 'status' => 'active']);

        $certificate = TransferCertificate::create([
            'school_id' => $admin->school_id,
            'serial' => (string) Str::uuid(),
            'student_id' => $student->id,
            'student_name' => $student->name,
            'class_name' => 'Six',
            'roll' => '1',
            'issue_date' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('school.students.tc.download', $certificate));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_id_card_template_two_preview_renders_with_sky_blue_front_and_back(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->get(route('school.students.id-card.preview', ['template' => 2]));

        $response->assertOk()
            ->assertSee('tpl2', false)
            ->assertSee('Student ID')
            ->assertSee("Father's Name", false)
            ->assertSee('Blood Group')
            ->assertSee('IF FOUND, PLEASE RETURN TO');
    }

    public function test_id_card_preview_shows_the_uploaded_signature_for_its_matching_position(): void
    {
        Storage::fake('public');
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->post(route('school.signatures.store'), [
            'title' => 'Principal',
            'position' => 'Student ID Card',
            'status' => 'active',
            'image' => UploadedFile::fake()->image('sign.png'),
        ]);

        $signature = Signature::first();

        $response = $this->actingAs($admin)->get(route('school.students.id-card.preview', ['template' => 2]));

        $response->assertOk()->assertSee($signature->imageUrl(), false);
    }
}
