<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Section;
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
    public const MODES = ['with-id', 'without-id'];

    private const COLUMNS = ['Roll No', 'Name', 'Gender', 'Religion', "Father's Name", "Mother's Name", 'Mobile No.'];

    public function create(): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $sections = Section::with('schoolClass')->get();
        $categories = ['Regular', 'Freedom Fighter Quota', 'Disabled Quota', 'Other'];
        $groups = ['Science', 'Commerce', 'Arts', 'General'];

        $previewWithId = Session::get($this->sessionKey('with-id'));
        $previewWithoutId = Session::get($this->sessionKey('without-id'));

        return view('school.students.import', compact(
            'academicYears', 'sections', 'categories', 'groups', 'previewWithId', 'previewWithoutId'
        ));
    }

    public function sample(string $mode): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->assertMode($mode);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = $mode === 'with-id' ? array_merge(['Student ID'], self::COLUMNS) : self::COLUMNS;

        $sheet->fromArray($headers, null, 'A1');

        $lastColumn = $sheet->getHighestColumn();
        $headerRange = "A1:{$lastColumn}1";

        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0B1F4E']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);

        $dataRange = "A1:{$lastColumn}31";
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setWidth(18);
        }
        $sheet->getRowDimension(1)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, "student-import-sample-{$mode}.xlsx");
    }

    public function preview(Request $request, string $mode): RedirectResponse
    {
        $this->assertMode($mode);

        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'group_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', function ($attribute, $value, $fail) {
                if (! in_array(strtolower($value->getClientOriginalExtension()), ['csv', 'xls', 'xlsx'], true)) {
                    $fail('The file must be a csv, xls, or xlsx file.');
                }
            }],
        ]);

        $section = Section::findOrFail($data['section_id']);

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
                'admission_number' => $mode === 'with-id'
                    ? trim((string) ($row[$this->colLetter($map, 'Student ID')] ?? ''))
                    : null,
                'name' => $name,
                'roll' => trim($row[$this->colLetter($map, 'Roll No')] ?? ''),
                'father_name' => trim($row[$this->colLetter($map, "Father's Name")] ?? ''),
                'mother_name' => trim($row[$this->colLetter($map, "Mother's Name")] ?? ''),
                'gender' => strtolower(trim($row[$this->colLetter($map, 'Gender')] ?? '')) ?: null,
                'religion' => trim($row[$this->colLetter($map, 'Religion')] ?? ''),
                'guardian_phone' => trim((string) ($row[$this->colLetter($map, 'Mobile No.')] ?? '')),
            ];
        }

        Session::put($this->sessionKey($mode), [
            'rows' => $parsed,
            'academic_year_id' => $data['academic_year_id'],
            'school_class_id' => $section->school_class_id,
            'section_id' => $section->id,
            'group_name' => $data['group_name'],
            'category' => $data['category'],
        ]);

        return redirect()->route('school.students.import.create')->with('status', count($parsed).' row(s) parsed. Review and click Save to import.');
    }

    public function store(string $mode): RedirectResponse
    {
        $this->assertMode($mode);

        $data = Session::get($this->sessionKey($mode));

        abort_unless($data, 404);

        $count = 0;
        foreach ($data['rows'] as $row) {
            if ($row['gender'] && ! in_array($row['gender'], ['male', 'female', 'other'])) {
                $row['gender'] = null;
            }

            if ($mode === 'without-id' || $row['admission_number'] === '') {
                $row['admission_number'] = null;
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

        Session::forget($this->sessionKey($mode));

        return redirect()->route('school.students.index')->with('status', "{$count} student(s) imported successfully.");
    }

    public function cancel(string $mode): RedirectResponse
    {
        $this->assertMode($mode);

        Session::forget($this->sessionKey($mode));

        return redirect()->route('school.students.import.create');
    }

    private function assertMode(string $mode): void
    {
        abort_unless(in_array($mode, self::MODES, true), 404);
    }

    private function sessionKey(string $mode): string
    {
        return 'student_import_preview_'.str_replace('-', '_', $mode);
    }

    private function colLetter(array $map, string $header): ?string
    {
        return $map[$header] ?? null;
    }
}
