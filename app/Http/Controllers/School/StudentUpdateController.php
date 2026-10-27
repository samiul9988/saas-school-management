<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Group;
use App\Models\ResultBatch;
use App\Models\ResultBatchResult;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentUpdateController extends Controller
{
    private function groups(): array
    {
        $groups = Group::where('status', 'enable')->orderBy('serial')->pluck('name')->all();

        return $groups ?: ['Science', 'Commerce', 'Arts', 'General'];
    }

    private function sections()
    {
        return Section::with('schoolClass')->orderBy('name')->get();
    }

    private function classes()
    {
        return SchoolClass::with('sections')->orderBy('sort_order')->get();
    }

    public function basic(Request $request): View
    {
        $sections = $this->sections();
        $section = null;
        $students = collect();

        if ($request->filled('section_id')) {
            $section = Section::findOrFail($request->section_id);
            $students = Student::where('section_id', $section->id)->orderByRoll()->get();
        }

        return view('school.students.update-basic', compact('sections', 'section', 'students'));
    }

    public function basicUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'student_id' => ['required', 'exists:students,id'],
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'religion' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'village' => ['nullable', 'string', 'max:255'],
            'post_office' => ['nullable', 'string', 'max:255'],
            'upazila_thana' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
        ]);

        Student::findOrFail($data['student_id'])->update(collect($data)->except(['section_id', 'student_id'])->all());

        return redirect()->route('school.students.update-basic', ['section_id' => $data['section_id']])->with('status', 'Student basic info updated.');
    }

    public function photo(Request $request): View
    {
        $sections = $this->sections();
        $section = null;
        $students = collect();

        if ($request->filled('section_id')) {
            $section = Section::findOrFail($request->section_id);
            $students = Student::where('section_id', $section->id)->orderByRoll()->get();
        }

        return view('school.students.update-photo', compact('sections', 'section', 'students'));
    }

    public function photoUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'student_id' => ['required', 'exists:students,id'],
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $student = Student::findOrFail($data['student_id']);

        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $path = $request->file('photo')->store('student-photos', 'public');
        $student->update(['photo' => $path]);

        return redirect()->route('school.students.update-photo', ['section_id' => $data['section_id']])->with('status', 'Student photo updated.');
    }

    public function academic(Request $request): View
    {
        $sections = $this->sections();
        $classes = $this->classes();
        $section = null;
        $showAll = $request->get('section_id') === 'all';
        $students = collect();

        if ($showAll) {
            $students = Student::orderByRoll()->get();
        } elseif ($request->filled('section_id')) {
            $section = Section::findOrFail($request->section_id);
            $students = Student::where('section_id', $section->id)->orderByRoll()->get();
        }

        $groups = $this->groups();

        return view('school.students.update-academic', compact('sections', 'classes', 'section', 'showAll', 'students', 'groups'));
    }

    public function academicUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', function ($attribute, $value, $fail) {
                if ($value !== 'all' && ! Section::whereKey($value)->exists()) {
                    $fail('The selected section is invalid.');
                }
            }],
            'student_id' => ['required', 'exists:students,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'new_section_id' => ['required', 'exists:sections,id'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'roll' => ['nullable', 'string', 'max:50'],
        ]);

        Student::findOrFail($data['student_id'])->update([
            'school_class_id' => $data['school_class_id'],
            'section_id' => $data['new_section_id'],
            'group_name' => $data['group_name'],
            'roll' => $data['roll'],
        ]);

        return redirect()->route('school.students.update-academic', ['section_id' => $data['section_id']])->with('status', 'Student academic info updated.');
    }

    public function migrationWithoutMerit(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $sections = $this->sections();
        $classes = $this->classes();
        $groups = $this->groups();

        $section = null;
        $groupName = $request->get('group_name');
        $students = collect();

        if ($request->filled('section_id')) {
            $section = Section::findOrFail($request->section_id);
            $students = Student::where('section_id', $section->id)
                ->when($groupName, fn ($q) => $q->where('group_name', $groupName))
                ->orderByRoll()
                ->get();
        }

        return view('school.students.migration-without-merit', compact(
            'academicYears', 'sections', 'classes', 'groups', 'section', 'groupName', 'students'
        ));
    }

    public function migrationWithoutMeritStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'group_name' => ['nullable', 'string'],
            'target_academic_year_id' => ['required', 'exists:academic_years,id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'rolls' => ['array'],
            'rolls.*' => ['nullable', 'string', 'max:50'],
        ]);

        foreach ($data['student_ids'] as $studentId) {
            Student::whereKey($studentId)->update([
                'academic_year_id' => $data['target_academic_year_id'],
                'roll' => $data['rolls'][$studentId] ?? null,
            ]);
        }

        return redirect()->route('school.students.migration-without-merit', $request->only('section_id', 'group_name'))
            ->with('status', count($data['student_ids']).' student(s) migrated.');
    }

    public function migrationWithMerit(Request $request): View
    {
        $sections = $this->sections();
        $classes = $this->classes();
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $meritPositions = ['Class Position', 'Section Position'];

        $section = null;
        $resultBatches = collect();
        $resultBatchId = $request->get('result_batch_id');
        $rows = collect();

        if ($request->filled('section_id')) {
            $section = Section::findOrFail($request->section_id);
            $resultBatches = ResultBatch::where('school_class_id', $section->school_class_id)->orderByDesc('id')->get();
        }

        if ($section && $resultBatchId) {
            $rows = ResultBatchResult::with('student')
                ->where('result_batch_id', $resultBatchId)
                ->where('section_id', $section->id)
                ->orderBy('position')
                ->get()
                ->filter(fn ($row) => $row->student !== null);
        }

        return view('school.students.migration-with-merit', compact(
            'sections', 'classes', 'academicYears', 'meritPositions', 'section', 'resultBatches', 'resultBatchId', 'rows'
        ));
    }

    public function migrationWithMeritStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'result_batch_id' => ['required', 'exists:result_batches,id'],
            'target_academic_year_id' => ['required', 'exists:academic_years,id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'rolls' => ['array'],
            'rolls.*' => ['nullable', 'string', 'max:50'],
        ]);

        foreach ($data['student_ids'] as $studentId) {
            Student::whereKey($studentId)->update([
                'academic_year_id' => $data['target_academic_year_id'],
                'roll' => $data['rolls'][$studentId] ?? null,
            ]);
        }

        return redirect()->route('school.students.migration-with-merit', $request->only('section_id', 'result_batch_id'))
            ->with('status', count($data['student_ids']).' student(s) migrated in merit order.');
    }

    public function enableDisable(Request $request): View
    {
        $sections = $this->sections();
        $mode = $request->get('mode', 'disable') === 'enable' ? 'enable' : 'disable';
        $section = null;
        $students = collect();

        if ($request->filled('section_id')) {
            $section = Section::findOrFail($request->section_id);
            $students = Student::where('section_id', $section->id)
                ->where('status', $mode === 'disable' ? 'active' : 'inactive')
                ->orderByRoll()
                ->get();
        }

        return view('school.students.enable-disable', compact('sections', 'mode', 'section', 'students'));
    }

    public function enableDisableStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'mode' => ['required', 'in:enable,disable'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        Student::whereIn('id', $data['student_ids'])->update([
            'status' => $data['mode'] === 'enable' ? 'active' : 'inactive',
        ]);

        return redirect()->route('school.students.enable-disable', ['section_id' => $data['section_id'], 'mode' => $data['mode']])
            ->with('status', count($data['student_ids']).' student(s) '.($data['mode'] === 'enable' ? 'enabled' : 'disabled').'.');
    }
}
