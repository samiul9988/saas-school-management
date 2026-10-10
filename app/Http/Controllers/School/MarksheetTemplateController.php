<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\MarksheetTemplateConfig;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MarksheetTemplateController extends Controller
{
    public const TEMPLATES = [
        1 => 'Two Column Marksheet',
        2 => 'Four Column Marksheet',
        3 => 'Six Column Marksheet',
        4 => 'Four Column Marksheet',
    ];

    public const MERIT_POSITIONS = ['Class Wise', 'Section Wise'];

    public const HEADERS = ['ACADEMIC TRANSCRIPT', 'ACADEMIC PROGRESS REPORT', 'MARKSHEET', 'TABULATION SHEET'];

    public function preview(int $number): View
    {
        abort_unless(array_key_exists($number, self::TEMPLATES), 404);

        $school = Auth::user()->school;
        $grades = Grade::orderByDesc('min_percent')->get();

        $subjectRows = [
            ['name' => "Bangla 1st Paper", 'full' => 100, 'highest' => 95, 'components' => [], 'total' => 85, 'letterGrade' => 'A+', 'gradePoint' => '5.0'],
            ['name' => "Bangla 2nd Paper", 'full' => 100, 'highest' => 92, 'components' => [], 'total' => 80, 'letterGrade' => 'A+', 'gradePoint' => '5.0'],
            ['name' => "English 1st Paper", 'full' => 100, 'highest' => 90, 'components' => [], 'total' => 76, 'letterGrade' => 'A', 'gradePoint' => '4.0'],
            ['name' => "Mathematics", 'full' => 100, 'highest' => 98, 'components' => [], 'total' => 88, 'letterGrade' => 'A+', 'gradePoint' => '5.0'],
        ];

        if (in_array($number, [2])) {
            foreach ($subjectRows as &$row) {
                $cq = (int) round($row['total'] * 0.5);
                $mcq = (int) round($row['total'] * 0.3);
                $practical = $row['total'] - $cq - $mcq;
                $row['components'] = [
                    ['label' => 'CQ', 'value' => $cq],
                    ['label' => 'MCQ', 'value' => $mcq],
                    ['label' => 'Practical', 'value' => $practical],
                ];
            }
            unset($row);
        }

        $totalFull = array_sum(array_column($subjectRows, 'full'));
        $totalObtained = array_sum(array_column($subjectRows, 'total'));

        $student = [
            'name' => 'John Doe',
            'father_name' => 'Robert Doe',
            'mother_name' => 'Jane Doe',
            'id' => '2333',
            'roll' => '1',
            'class' => 'Nine',
            'section' => 'A',
            'group_name' => 'Science',
            'contact' => '01700000000',
            'dob' => '2010-01-01',
            'photo' => null,
        ];

        return view('school.mark-sheets.pdf.academic-transcript', [
            'variant' => $number,
            'school' => $school,
            'student' => $student,
            'examName' => 'Special Evaluation',
            'yearSession' => date('Y'),
            'subjectRows' => $subjectRows,
            'totals' => [
                'full' => $totalFull,
                'obtained' => $totalObtained,
                'gpa' => '5.0',
                'letterGrade' => 'A+',
                'resultStatus' => 'Passed',
            ],
            'meta' => [
                'failedSubjects' => 0,
                'workingDays' => 180,
                'totalPresent' => 170,
                'totalAbsent' => 10,
                'classPosition' => 1,
                'shiftPosition' => 1,
                'sectionPosition' => 1,
                'totalStudents' => 40,
            ],
            'grades' => $grades,
            'qrDataUri' => null,
            'publishDate' => now()->format('d-M-y'),
        ]);
    }

    public function index(): View
    {
        $templates = self::TEMPLATES;

        $configs = MarksheetTemplateConfig::with('schoolClass')->orderByDesc('id')->get();
        $classes = SchoolClass::orderBy('sort_order')->get();
        $meritPositions = self::MERIT_POSITIONS;
        $headers = self::HEADERS;

        return view('school.subjects.marksheet-template', compact('templates', 'configs', 'classes', 'meritPositions', 'headers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'template_number' => ['required', 'integer', 'in:'.implode(',', array_keys(self::TEMPLATES))],
            'marksheet_header' => ['required', 'in:'.implode(',', self::HEADERS)],
            'mark_sheet_size' => ['required', 'in:'.implode(',', self::MERIT_POSITIONS)],
            'student_img_status' => ['required', 'in:yes,no'],
            'watermark_status' => ['required', 'in:yes,no'],
        ]);

        MarksheetTemplateConfig::updateOrCreate(
            ['school_class_id' => $data['school_class_id']],
            [
                'template_number' => $data['template_number'],
                'marksheet_header' => $data['marksheet_header'],
                'mark_sheet_size' => $data['mark_sheet_size'],
                'student_img_status' => $data['student_img_status'] === 'yes',
                'watermark_status' => $data['watermark_status'] === 'yes',
            ]
        );

        return redirect()->route('school.exam-configuration.marksheet-template')->with('status', 'Marksheet template assigned.');
    }

    public function destroy(MarksheetTemplateConfig $marksheetTemplateConfig): RedirectResponse
    {
        $marksheetTemplateConfig->delete();

        return redirect()->route('school.exam-configuration.marksheet-template')->with('status', 'Marksheet template removed.');
    }
}
