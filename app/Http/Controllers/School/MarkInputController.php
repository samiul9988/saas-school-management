<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamShortCode;
use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MarkInputController extends Controller
{
    public const GROUPS = ['Science', 'Commerce', 'Arts', 'General'];

    public function index(Request $request): View
    {
        $sections = Section::with('schoolClass')->get();
        $exams = Exam::orderByDesc('starts_on')->get();
        $subjects = Subject::orderBy('name')->get();
        $shortCodes = ExamShortCode::orderBy('serial')->get();
        $groups = self::GROUPS;

        $section = null;
        $examSubject = null;
        $components = collect();
        $students = collect();
        $marks = collect();
        $error = null;

        if ($request->filled(['section_id', 'exam_id', 'subject_id'])) {
            $data = $request->validate([
                'section_id' => ['required', 'exists:sections,id'],
                'exam_id' => ['required', 'exists:exams,id'],
                'subject_id' => ['required', 'exists:subjects,id'],
                'group_name' => ['nullable', 'string'],
            ]);

            $section = Section::with('schoolClass')->findOrFail($data['section_id']);

            $examSubject = ExamSubject::where('exam_id', $data['exam_id'])
                ->where('school_class_id', $section->school_class_id)
                ->where('subject_id', $data['subject_id'])
                ->first();

            if (! $examSubject) {
                $error = 'This subject is not configured for the selected exam and class yet. Add it from Exams first.';
            } else {
                $components = $examSubject->subject->components()->with('shortCode')->get();

                $students = Student::where('section_id', $section->id)
                    ->where('status', 'active')
                    ->when($request->filled('group_name'), fn ($q) => $q->where('group_name', $data['group_name']))
                    ->orderByRoll()
                    ->get();

                $marks = Mark::where('exam_subject_id', $examSubject->id)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->with('components')
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('school.marks.input', compact(
            'sections', 'exams', 'subjects', 'shortCodes', 'groups',
            'section', 'examSubject', 'components', 'students', 'marks', 'error'
        ));
    }

    public function update(Request $request, ExamSubject $examSubject): RedirectResponse
    {
        $data = $request->validate([
            'marks' => ['required', 'array'],
            'marks.*' => ['array'],
            'marks.*.*' => ['nullable', 'numeric', 'min:0'],
            'redirect' => ['nullable', 'string'],
        ]);

        $components = $examSubject->subject->components()->get()->keyBy('id');

        DB::transaction(function () use ($data, $examSubject, $components) {
            foreach ($data['marks'] as $studentId => $componentMarks) {
                $total = 0;
                $hasAny = false;

                $mark = Mark::updateOrCreate(
                    ['exam_subject_id' => $examSubject->id, 'student_id' => $studentId],
                    []
                );

                foreach ($componentMarks as $componentId => $value) {
                    if (! $components->has($componentId)) {
                        continue;
                    }

                    if ($value !== null && $value !== '') {
                        $hasAny = true;
                        $total += (float) $value;
                    }

                    $mark->components()->updateOrCreate(
                        ['subject_component_id' => $componentId],
                        ['marks_obtained' => $value === null || $value === '' ? null : $value]
                    );
                }

                $mark->update([
                    'marks_obtained' => $hasAny ? $total : null,
                    'is_absent' => ! $hasAny,
                ]);
            }
        });

        return redirect()->back()->with('status', 'Marks saved successfully.');
    }

    public function downloadTemplate(Request $request): StreamedResponse|RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'group_name' => ['nullable', 'string'],
        ]);

        $section = Section::with('schoolClass')->findOrFail($data['section_id']);

        $examSubject = ExamSubject::with('exam', 'subject')
            ->where('exam_id', $data['exam_id'])
            ->where('school_class_id', $section->school_class_id)
            ->where('subject_id', $data['subject_id'])
            ->first();

        if (! $examSubject) {
            return redirect()->route('school.mark-input.index', $request->only('section_id', 'exam_id', 'subject_id', 'group_name'))
                ->with('error', 'This subject is not configured for the selected exam and class yet. Add it from Exams first.');
        }

        $components = $examSubject->subject->components()->with('shortCode')->get();

        $students = Student::where('section_id', $section->id)
            ->where('status', 'active')
            ->when($request->filled('group_name'), fn ($q) => $q->where('group_name', $data['group_name']))
            ->orderByRoll()
            ->get();

        $marks = Mark::where('exam_subject_id', $examSubject->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->with('components')
            ->get()
            ->keyBy('student_id');

        $headers = array_merge(
            ['Auto Generated', 'Student Name', 'Student ID', 'Roll'],
            $components->pluck('shortCode.name')->all()
        );

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mark Input Excel Sheet');
        $sheet->fromArray($headers, null, 'A1');

        $rowNumber = 2;
        foreach ($students as $student) {
            $existing = $marks->get($student->id);

            $row = [$student->id, $student->name, $student->admission_number, $student->roll];

            foreach ($components as $component) {
                $value = $existing?->components->firstWhere('subject_component_id', $component->id)?->marks_obtained;
                $row[] = $value ?? '';
            }

            $sheet->fromArray($row, null, "A{$rowNumber}");
            $rowNumber++;
        }

        $lastColumn = $sheet->getHighestColumn();
        $lastRow = max($rowNumber - 1, 1);

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setWidth(16);
        }

        $sectionLabel = trim(($section->schoolClass?->name ? $section->schoolClass->name.' ' : '').$section->name);
        $filenameParts = [$sectionLabel, $data['group_name'] ?? 'All', $examSubject->exam->name, $examSubject->subject->name];
        $filename = 'mark_input_blanksheet_'.implode('_', array_map(fn ($part) => str_replace(['/', '\\'], '-', $part), $filenameParts)).'.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    public function upload(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'file' => ['required', 'file', function ($attribute, $value, $fail) {
                if (! in_array(strtolower($value->getClientOriginalExtension()), ['csv', 'xls', 'xlsx'], true)) {
                    $fail('The file must be a csv, xls, or xlsx file.');
                }
            }],
        ]);

        $section = Section::findOrFail($data['section_id']);

        $examSubject = ExamSubject::where('exam_id', $data['exam_id'])
            ->where('school_class_id', $section->school_class_id)
            ->where('subject_id', $data['subject_id'])
            ->first();

        if (! $examSubject) {
            return redirect()->back()->with('error', 'This subject is not configured for the selected exam and class yet. Add it from Exams first.');
        }

        $components = $examSubject->subject->components()->with('shortCode')->get();
        $shortCodeColumns = $components->keyBy(fn ($component) => $component->shortCode->name);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $header = array_map('trim', array_shift($rows) ?? []);
        $map = array_flip($header);

        DB::transaction(function () use ($rows, $map, $examSubject, $shortCodeColumns) {
            foreach ($rows as $row) {
                $studentId = $map['Auto Generated'] ?? null ? trim((string) ($row[$map['Auto Generated']] ?? '')) : null;

                if (! $studentId || ! Student::whereKey($studentId)->exists()) {
                    continue;
                }

                $mark = Mark::firstOrCreate(['exam_subject_id' => $examSubject->id, 'student_id' => $studentId]);

                $total = 0;
                $hasAny = false;

                foreach ($shortCodeColumns as $name => $component) {
                    if (! isset($map[$name])) {
                        continue;
                    }

                    $cell = trim((string) ($row[$map[$name]] ?? ''));
                    $value = $cell === '' ? null : (float) $cell;

                    if ($value !== null) {
                        $hasAny = true;
                        $total += $value;
                    }

                    $mark->components()->updateOrCreate(
                        ['subject_component_id' => $component->id],
                        ['marks_obtained' => $value]
                    );
                }

                $mark->update(['marks_obtained' => $hasAny ? $total : null, 'is_absent' => ! $hasAny]);
            }
        });

        return redirect()->back()->with('status', 'Marks uploaded successfully.');
    }
}
