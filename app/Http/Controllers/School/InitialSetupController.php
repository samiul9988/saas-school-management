<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Group;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Shift;
use App\Models\StudentCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InitialSetupController extends Controller
{
    public const TYPES = [
        'academic-year' => ['model' => AcademicYear::class, 'label' => 'Academic Year', 'placeholder' => 'Enter Academic Year', 'needsClass' => false],
        'class' => ['model' => SchoolClass::class, 'label' => 'Class', 'placeholder' => 'Enter Class Name', 'needsClass' => false],
        'shift' => ['model' => Shift::class, 'label' => 'Shift', 'placeholder' => 'Enter Shift Name', 'needsClass' => false],
        'section' => ['model' => Section::class, 'label' => 'Section', 'placeholder' => 'Enter Section Name', 'needsClass' => true],
        'group' => ['model' => Group::class, 'label' => 'Group', 'placeholder' => 'Enter Group Name', 'needsClass' => false],
        'student-category' => ['model' => StudentCategory::class, 'label' => 'Student Category', 'placeholder' => 'Enter Category Name', 'needsClass' => false],
        'period' => ['model' => Period::class, 'label' => 'Period', 'placeholder' => 'Enter Period Name', 'needsClass' => false],
    ];

    public function index(string $type): View
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);

        $meta = self::TYPES[$type];
        $modelClass = $meta['model'];

        $query = $modelClass::query()->orderBy('serial')->orderBy('id');
        if ($meta['needsClass']) {
            $query->with('schoolClass');
        }
        $records = $query->get();

        $classes = $meta['needsClass'] ? SchoolClass::orderBy('sort_order')->get() : collect();

        return view('school.students.initial-setup.index', [
            'type' => $type,
            'meta' => $meta,
            'records' => $records,
            'classes' => $classes,
            'types' => self::TYPES,
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);
        $meta = self::TYPES[$type];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'integer', 'min:0'],
        ];
        if ($meta['needsClass']) {
            $rules['school_class_id'] = ['required', 'exists:school_classes,id'];
        }

        $data = $request->validate($rules);
        $data['serial'] = $data['serial'] ?? 0;

        $payload = ['name' => $data['name'], 'serial' => $data['serial']];
        if ($type === 'class') {
            $payload['sort_order'] = $data['serial'];
        }
        if ($type === 'academic-year') {
            $payload = array_merge($payload, $this->academicYearDates($data['name']));
        }
        if ($meta['needsClass']) {
            $payload['school_class_id'] = $data['school_class_id'];
        }

        $meta['model']::create($payload);

        return redirect()->route('school.students.initial-setup', $type)->with('status', $meta['label'].' added.');
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);
        $meta = self::TYPES[$type];
        $record = $meta['model']::findOrFail($id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'integer', 'min:0'],
        ];
        if ($meta['needsClass']) {
            $rules['school_class_id'] = ['required', 'exists:school_classes,id'];
        }

        $data = $request->validate($rules);
        $data['serial'] = $data['serial'] ?? 0;

        $payload = ['name' => $data['name'], 'serial' => $data['serial']];
        if ($type === 'class') {
            $payload['sort_order'] = $data['serial'];
        }
        if ($type === 'academic-year') {
            $payload = array_merge($payload, $this->academicYearDates($data['name']));
        }
        if ($meta['needsClass']) {
            $payload['school_class_id'] = $data['school_class_id'];
        }
        $payload['status'] = $request->boolean('status') ? 'enable' : 'disable';

        $record->update($payload);

        return redirect()->route('school.students.initial-setup', $type)->with('status', $meta['label'].' updated.');
    }

    private function academicYearDates(string $name): array
    {
        if (preg_match('/^\d{4}$/', $name)) {
            return ['starts_on' => $name.'-01-01', 'ends_on' => $name.'-12-31'];
        }

        return ['starts_on' => now()->startOfYear()->toDateString(), 'ends_on' => now()->endOfYear()->toDateString()];
    }

    public function toggleStatus(string $type, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);
        $meta = self::TYPES[$type];
        $record = $meta['model']::findOrFail($id);

        $record->update(['status' => $record->status === 'enable' ? 'disable' : 'enable']);

        return redirect()->route('school.students.initial-setup', $type);
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);
        $meta = self::TYPES[$type];
        $meta['model']::findOrFail($id)->delete();

        return redirect()->route('school.students.initial-setup', $type)->with('status', $meta['label'].' removed.');
    }
}
