<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentImportController extends Controller
{
    private const SESSION_KEY = 'student_import_preview';

    private const COLUMNS = ['Roll No', "Father's Name", "Mother's Name", 'Gender', 'Religion', 'Mobile No.'];

    public function create(): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $classes = SchoolClass::with('sections')->orderBy('sort_order')->get();
        $preview = Session::get(self::SESSION_KEY);

        return view('school.students.import', compact('academicYears', 'classes', 'preview'));
    }

    public function sample(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(array_merge(['Name'], self::COLUMNS), null, 'A1');
        $sheet->fromArray(['John Doe', '1', 'Robert Doe', 'Jane Doe', 'Male', 'Islam', '01700000000'], null, 'A2');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'student-import-sample.xlsx');
    }

    public function preview(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $header = array_map('trim', array_shift($rows) ?? []);
        $map = array_flip($header);

        $parsed = [];
        foreach ($rows as $row) {
            $name = trim($row[$this->colLetter($map, 'Name')] ?? '');

            if ($name === '') {
                continue;
            }

            $parsed[] = [
                'name' => $name,
                'roll' => trim($row[$this->colLetter($map, 'Roll No')] ?? ''),
                'father_name' => trim($row[$this->colLetter($map, "Father's Name")] ?? ''),
                'mother_name' => trim($row[$this->colLetter($map, "Mother's Name")] ?? ''),
                'gender' => strtolower(trim($row[$this->colLetter($map, 'Gender')] ?? '')) ?: null,
                'religion' => trim($row[$this->colLetter($map, 'Religion')] ?? ''),
                'guardian_phone' => trim((string) ($row[$this->colLetter($map, 'Mobile No.')] ?? '')),
            ];
        }

        Session::put(self::SESSION_KEY, [
            'rows' => $parsed,
            'academic_year_id' => $data['academic_year_id'] ?? null,
            'school_class_id' => $data['school_class_id'],
            'section_id' => $data['section_id'] ?? null,
            'group_name' => $data['group_name'] ?? null,
            'category' => $data['category'] ?? null,
        ]);

        return redirect()->route('school.students.import.create')->with('status', count($parsed).' row(s) parsed. Review and click Save to import.');
    }

    public function store(): RedirectResponse
    {
        $data = Session::get(self::SESSION_KEY);

        abort_unless($data, 404);

        $count = 0;
        foreach ($data['rows'] as $row) {
            if ($row['gender'] && ! in_array($row['gender'], ['male', 'female', 'other'])) {
                $row['gender'] = null;
            }

            Student::create([
                ...$row,
                'academic_year_id' => $data['academic_year_id'],
                'school_class_id' => $data['school_class_id'],
                'section_id' => $data['section_id'],
                'group_name' => $data['group_name'],
                'category' => $data['category'],
                'status' => 'active',
            ]);
            $count++;
        }

        Session::forget(self::SESSION_KEY);

        return redirect()->route('school.students.index')->with('status', "{$count} student(s) imported successfully.");
    }

    public function cancel(): RedirectResponse
    {
        Session::forget(self::SESSION_KEY);

        return redirect()->route('school.students.import.create');
    }

    private function colLetter(array $map, string $header): ?string
    {
        return $map[$header] ?? null;
    }
}
