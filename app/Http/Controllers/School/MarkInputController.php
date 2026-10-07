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
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
                    ->orderBy('roll')
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

    public function downloadTemplate(Request $request): Response
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'group_name' => ['nullable', 'string'],
        ]);

        $section = Section::findOrFail($data['section_id']);

        $examSubject = ExamSubject::where('exam_id', $data['exam_id'])
            ->where('school_class_id', $section->school_class_id)
            ->where('subject_id', $data['subject_id'])
            ->firstOrFail();

        $components = $examSubject->subject->components()->with('shortCode')->get();

        $students = Student::where('section_id', $section->id)
            ->where('status', 'active')
            ->when($request->filled('group_name'), fn ($q) => $q->where('group_name', $data['group_name']))
            ->orderBy('roll')
            ->get();

        $marks = Mark::where('exam_subject_id', $examSubject->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->with('components')
            ->get()
            ->keyBy('student_id');

        $headers = array_merge(['ID', 'Name', 'Roll'], $components->pluck('shortCode.name')->all());

        $rows = $students->map(function ($student) use ($components, $marks) {
            $row = [$student->id, $student->name, $student->roll];
            $existing = $marks->get($student->id);

            foreach ($components as $component) {
                $value = $existing?->components->firstWhere('subject_component_id', $component->id)?->marks_obtained;
                $row[] = $value ?? 0;
            }

            return $row;
        });

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mark-input-template.csv"',
        ]);
    }

    public function upload(Request $request, ExamSubject $examSubject): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $components = $examSubject->subject->components()->with('shortCode')->get();
        $shortCodeColumns = $components->keyBy(fn ($component) => $component->shortCode->name);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle);

        DB::transaction(function () use ($handle, $header, $examSubject, $shortCodeColumns) {
            while (($row = fgetcsv($handle)) !== false) {
                $cells = array_combine($header, $row);
                $studentId = $cells['ID'] ?? null;

                if (! $studentId || ! Student::whereKey($studentId)->exists()) {
                    continue;
                }

                $mark = Mark::firstOrCreate(['exam_subject_id' => $examSubject->id, 'student_id' => $studentId]);

                $total = 0;
                $hasAny = false;

                foreach ($shortCodeColumns as $name => $component) {
                    if (! array_key_exists($name, $cells)) {
                        continue;
                    }

                    $value = $cells[$name] === '' ? null : (float) $cells[$name];

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

        fclose($handle);

        return redirect()->back()->with('status', 'Marks uploaded successfully.');
    }
}
