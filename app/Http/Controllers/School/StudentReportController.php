<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentReportController extends Controller
{
    public const SEARCH_COLUMNS = [
        'admission_number' => 'Student ID',
        'name' => 'Name',
        'school_class_id' => 'Class',
        'group_name' => 'Group',
        'roll' => 'Roll',
        'gender' => 'Gender',
        'religion' => 'Religion',
    ];

    public function allStudent(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $searchColumn = $request->get('search_column');
        $searchValue = $request->get('search_value');

        $students = Student::with('schoolClass')
            ->when($searchColumn && $searchValue, function ($q) use ($searchColumn, $searchValue) {
                if ($searchColumn === 'school_class_id') {
                    $q->whereHas('schoolClass', fn ($c) => $c->where('name', 'like', "%{$searchValue}%"));
                } else {
                    $q->where($searchColumn, 'like', "%{$searchValue}%");
                }
            })
            ->orderBy('roll')
            ->get();

        $searchColumns = self::SEARCH_COLUMNS;

        return view('school.students.report.all', compact('students', 'classes', 'searchColumns', 'searchColumn', 'searchValue'));
    }

    public function sectionWiseSummary(): View
    {
        $sections = Section::with('schoolClass')->withCount('students')->orderBy('name')->get();
        $total = $sections->sum('students_count');

        return view('school.students.report.section-summary', compact('sections', 'total'));
    }

    public function sectionWise(Request $request): View
    {
        $sections = Section::with('schoolClass')->orderBy('name')->get();
        $section = null;
        $students = collect();

        if ($request->filled('section_id')) {
            $section = Section::findOrFail($request->section_id);
            $students = Student::where('section_id', $section->id)->orderBy('roll')->get();
        }

        return view('school.students.report.section-wise', compact('sections', 'section', 'students'));
    }

    public function sectionWisePdf(Request $request, string $mode): Response
    {
        $section = Section::with('schoolClass')->findOrFail($request->section_id);
        $students = Student::where('section_id', $section->id)->orderBy('roll')->get();
        $school = Auth::user()->school;
        $withImage = $mode === 'with-image';

        $pdf = Pdf::loadView('school.students.report.pdf.section-wise', compact('school', 'section', 'students', 'withImage'));

        return $pdf->download('Section Wise Student Report '.$section->schoolClass?->name.'_'.$section->name.'.pdf');
    }

    public function sectionWiseExcel(Request $request): StreamedResponse
    {
        $section = Section::with('schoolClass')->findOrFail($request->section_id);
        $students = Student::where('section_id', $section->id)->orderBy('roll')->get();

        $headers = ['ID', 'Name', 'Roll', 'Father', 'Mother', 'Gender', 'Religion', 'Mobile', 'Village', 'Upazila'];
        $rows = $students->map(fn ($s) => [
            $s->admission_number, $s->name, $s->roll, $s->father_name, $s->mother_name,
            ucfirst((string) $s->gender), $s->religion, $s->guardian_phone, $s->village, $s->upazila_thana,
        ])->all();

        return $this->excelDownload('Section Wise Student Report '.$section->name, $headers, $rows);
    }

    public function classWise(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $class = null;
        $students = collect();

        if ($request->filled('school_class_id')) {
            $class = SchoolClass::findOrFail($request->school_class_id);
            $students = Student::with('section')->where('school_class_id', $class->id)->orderBy('roll')->get();
        }

        return view('school.students.report.class-wise', compact('classes', 'class', 'students'));
    }

    public function classWisePdf(Request $request): Response
    {
        $class = SchoolClass::findOrFail($request->school_class_id);
        $students = Student::with('section')->where('school_class_id', $class->id)->orderBy('roll')->get();
        $school = Auth::user()->school;

        $pdf = Pdf::loadView('school.students.report.pdf.class-wise', compact('school', 'class', 'students'));

        return $pdf->download('Class Wise Student Report '.$class->name.'.pdf');
    }

    public function classWiseExcel(Request $request): StreamedResponse
    {
        $class = SchoolClass::findOrFail($request->school_class_id);
        $students = Student::with('section')->where('school_class_id', $class->id)->orderBy('roll')->get();

        $headers = ['ID', 'Name', 'Class', 'Section', 'Roll', 'Group', 'Father', 'Mother', 'Gender', 'Religion', 'Mobile No.'];
        $rows = $students->map(fn ($s) => [
            $s->admission_number, $s->name, $class->name, $s->section?->name, $s->roll, $s->group_name,
            $s->father_name, $s->mother_name, ucfirst((string) $s->gender), $s->religion, $s->guardian_phone,
        ])->all();

        return $this->excelDownload('Class Wise Student Report '.$class->name, $headers, $rows);
    }

    public function groupWise(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $groups = Group::where('status', 'enable')->orderBy('serial')->pluck('name')->all() ?: ['Science', 'Commerce', 'Arts', 'General'];
        $class = null;
        $groupName = $request->get('group_name');
        $students = collect();

        if ($request->filled('school_class_id') && $groupName) {
            $class = SchoolClass::findOrFail($request->school_class_id);
            $students = Student::where('school_class_id', $class->id)->where('group_name', $groupName)->orderBy('roll')->get();
        }

        return view('school.students.report.group-wise', compact('classes', 'groups', 'class', 'groupName', 'students'));
    }

    public function groupWisePdf(Request $request): Response
    {
        $class = SchoolClass::findOrFail($request->school_class_id);
        $groupName = $request->group_name;
        $students = Student::where('school_class_id', $class->id)->where('group_name', $groupName)->orderBy('roll')->get();
        $school = Auth::user()->school;

        $pdf = Pdf::loadView('school.students.report.pdf.group-wise', compact('school', 'class', 'groupName', 'students'));

        return $pdf->download('Group Wise Student Report '.$groupName.'.pdf');
    }

    public function groupWiseExcel(Request $request): StreamedResponse
    {
        $class = SchoolClass::findOrFail($request->school_class_id);
        $groupName = $request->group_name;
        $students = Student::where('school_class_id', $class->id)->where('group_name', $groupName)->orderBy('roll')->get();

        $headers = ['ID', 'Name', 'Roll', 'Group', 'Father', 'Mother', 'Gender', 'Religion', 'Mobile'];
        $rows = $students->map(fn ($s) => [
            $s->admission_number, $s->name, $s->roll, $s->group_name, $s->father_name, $s->mother_name,
            ucfirst((string) $s->gender), $s->religion, $s->guardian_phone,
        ])->all();

        return $this->excelDownload('Group Wise Student Report '.$groupName, $headers, $rows);
    }

    public function genderWise(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $year = $request->get('year');
        $rows = collect();

        if ($year) {
            $rows = SchoolClass::orderBy('sort_order')->get()->map(function ($class) {
                $students = Student::where('school_class_id', $class->id)->get();

                return [
                    'class' => $class->name,
                    'total' => $students->count(),
                    'male' => $students->where('gender', 'male')->count(),
                    'female' => $students->where('gender', 'female')->count(),
                    'other' => $students->where('gender', 'other')->count(),
                ];
            })->filter(fn ($row) => $row['total'] > 0)->values();
        }

        return view('school.students.report.gender-wise', compact('academicYears', 'year', 'rows'));
    }

    public function genderWisePdf(Request $request): Response
    {
        $year = $request->get('year');
        $school = Auth::user()->school;

        $rows = SchoolClass::orderBy('sort_order')->get()->map(function ($class) {
            $students = Student::where('school_class_id', $class->id)->get();

            return [
                'class' => $class->name,
                'total' => $students->count(),
                'male' => $students->where('gender', 'male')->count(),
                'female' => $students->where('gender', 'female')->count(),
                'other' => $students->where('gender', 'other')->count(),
            ];
        })->filter(fn ($row) => $row['total'] > 0)->values();

        $pdf = Pdf::loadView('school.students.report.pdf.gender-wise', compact('school', 'year', 'rows'));

        return $pdf->download('Gender Wise Student Summary (By Class) _ '.$year.'.pdf');
    }

    public function religionWise(): View
    {
        $religions = Student::whereNotNull('religion')->distinct()->pluck('religion')->filter()->values();

        $rows = SchoolClass::orderBy('sort_order')->get()->map(function ($class) use ($religions) {
            $students = Student::where('school_class_id', $class->id)->get();
            $row = ['class' => $class->name];
            foreach ($religions as $religion) {
                $row[$religion] = $students->where('religion', $religion)->count();
            }
            $row['total'] = $students->count();

            return $row;
        })->filter(fn ($row) => $row['total'] > 0)->values();

        $total = Student::count();

        return view('school.students.report.religion-wise', compact('religions', 'rows', 'total'));
    }

    public function religionWisePdf(): Response
    {
        $school = Auth::user()->school;
        $religions = Student::whereNotNull('religion')->distinct()->pluck('religion')->filter()->values();

        $rows = SchoolClass::orderBy('sort_order')->get()->map(function ($class) use ($religions) {
            $students = Student::where('school_class_id', $class->id)->get();
            $row = ['class' => $class->name];
            foreach ($religions as $religion) {
                $row[$religion] = $students->where('religion', $religion)->count();
            }
            $row['total'] = $students->count();

            return $row;
        })->filter(fn ($row) => $row['total'] > 0)->values();

        $pdf = Pdf::loadView('school.students.report.pdf.religion-wise', compact('school', 'religions', 'rows'));

        return $pdf->download('Student_Summary(Religion_Wise).pdf');
    }

    private function excelDownload(string $title, array $headers, array $rows): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($title, 0, 31));

        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($rows, null, 'A2');

        $lastColumn = chr(ord('A') + count($headers) - 1);
        $headerRange = "A1:{$lastColumn}1";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0B1F4E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $fullRange = "A1:{$lastColumn}".(count($rows) + 1);
        $sheet->getStyle($fullRange)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = $title.'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
