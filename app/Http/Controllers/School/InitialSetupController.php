<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Group;
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
        'section' => ['model' => Section::class, 'label' => 'Section', 'placeholder' => 'Enter Section Name', 'needsClass' => false],
        'group' => ['model' => Group::class, 'label' => 'Group', 'placeholder' => 'Enter Group Name', 'needsClass' => false],
        'student-category' => ['model' => StudentCategory::class, 'label' => 'Student Category', 'placeholder' => 'Enter Category Name', 'needsClass' => false],
    ];

    public function index(string $type): View
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);

        $meta = self::TYPES[$type];
        $modelClass = $meta['model'];

        $query = $modelClass::query()->orderBy('serial')->orderBy('id');
        if ($type === 'section') {
            // This page manages section *names* only (one row per name); the
            // per-class rows created from Configuration > Class are a
            // separate concern and shouldn't appear here as look-alike
            // duplicates of the name that was actually typed in.
            $query->withoutGlobalScope('assigned')->whereNull('school_class_id');
        }
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

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['serial'] = $data['serial'] ?? 0;

        $payload = ['name' => $data['name'], 'serial' => $data['serial']];
        if ($type === 'class') {
            $payload['sort_order'] = $data['serial'];
        }
        if ($type === 'academic-year') {
            $payload = array_merge($payload, $this->academicYearDates($data['name']));
        }

        $meta['model']::create($payload);

        return redirect()->route('school.students.initial-setup', $type)->with('status', $meta['label'].' added.');
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);
        $meta = self::TYPES[$type];
        $record = $this->findRecord($type, $meta, $id);

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
        $record = $this->findRecord($type, $meta, $id);

        $record->update(['status' => $record->status === 'enable' ? 'disable' : 'enable']);

        return redirect()->route('school.students.initial-setup', $type);
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);
        $meta = self::TYPES[$type];
        $this->findRecord($type, $meta, $id)->delete();

        return redirect()->route('school.students.initial-setup', $type)->with('status', $meta['label'].' removed.');
    }

    private function findRecord(string $type, array $meta, int $id)
    {
        $query = $meta['model']::query();

        if ($type === 'section') {
            $query->withoutGlobalScope('assigned')->whereNull('school_class_id');
        }

        return $query->findOrFail($id);
    }
}
