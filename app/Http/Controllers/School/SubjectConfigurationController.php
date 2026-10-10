<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubjectConfigurationController extends Controller
{
    public const GROUPS = ['Science', 'Commerce', 'Arts', 'General'];

    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $groups = self::GROUPS;
        $subjects = Subject::orderBy('name')->get();
        $subjectTypes = SubjectAssignment::TYPES;

        $class = null;
        $groupName = null;
        $assignedSubjectIds = [];
        $assignments = collect();

        if ($request->filled(['school_class_id', 'group_name'])) {
            $data = $request->validate([
                'school_class_id' => ['required', 'exists:school_classes,id'],
                'group_name' => ['required', 'string'],
            ]);

            $class = SchoolClass::findOrFail($data['school_class_id']);
            $groupName = $data['group_name'];

            $assignments = SubjectAssignment::where('school_class_id', $class->id)
                ->where('group_name', $groupName)
                ->with('subject')
                ->orderBy('serial')
                ->get();

            $assignedSubjectIds = $assignments->pluck('subject_id')->all();
        }

        return view('school.subjects.configuration', compact(
            'classes', 'groups', 'subjects', 'subjectTypes', 'class', 'groupName', 'assignedSubjectIds', 'assignments'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'group_name' => ['required', 'string'],
            'subject_ids' => ['array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);

        $class = SchoolClass::findOrFail($data['school_class_id']);
        $subjectIds = $data['subject_ids'] ?? [];

        DB::transaction(function () use ($class, $data, $subjectIds) {
            $existing = SubjectAssignment::where('school_class_id', $class->id)
                ->where('group_name', $data['group_name'])
                ->get()
                ->keyBy('subject_id');

            // Remove unchecked subjects
            $existing->whereNotIn('subject_id', $subjectIds)->each->delete();

            // Add newly checked subjects
            $nextSerial = $existing->max('serial') ?? 0;

            foreach ($subjectIds as $subjectId) {
                if ($existing->has($subjectId)) {
                    continue;
                }

                $nextSerial++;

                SubjectAssignment::create([
                    'school_class_id' => $class->id,
                    'group_name' => $data['group_name'],
                    'subject_id' => $subjectId,
                    'subject_type' => 'Compulsory',
                    'serial' => $nextSerial,
                    'merge_id' => 0,
                ]);
            }
        });

        return redirect()->route('school.exam-configuration.subject', $request->only('school_class_id', 'group_name'))
            ->with('status', 'Subject configuration saved.');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'group_name' => ['required', 'string'],
            'assignments' => ['array'],
            'assignments.*.subject_type' => ['required', 'in:'.implode(',', SubjectAssignment::TYPES)],
            'assignments.*.serial' => ['required', 'integer', 'min:0'],
            'assignments.*.merge_id' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['assignments'] ?? [] as $id => $values) {
                SubjectAssignment::where('id', $id)->update([
                    'subject_type' => $values['subject_type'],
                    'serial' => $values['serial'],
                    'merge_id' => $values['merge_id'],
                ]);
            }
        });

        return redirect()->route('school.exam-configuration.subject', $request->only('school_class_id', 'group_name'))
            ->with('status', 'Subjects updated.');
    }

    public function destroy(Request $request, SubjectAssignment $subjectAssignment): RedirectResponse
    {
        $schoolClassId = $subjectAssignment->school_class_id;
        $groupName = $subjectAssignment->group_name;

        $subjectAssignment->delete();

        return redirect()->route('school.exam-configuration.subject', [
            'school_class_id' => $schoolClassId,
            'group_name' => $groupName,
        ])->with('status', 'Subject removed.');
    }
}
