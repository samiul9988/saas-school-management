<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ClassGroupAssignment;
use App\Models\Group;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupConfigurationController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $groups = Group::orderBy('serial')->get();

        $selectedClassId = $request->get('school_class_id');
        $selectedGroupIds = $selectedClassId
            ? ClassGroupAssignment::where('school_class_id', $selectedClassId)->pluck('group_id')->all()
            : [];

        $assignments = ClassGroupAssignment::with('schoolClass', 'group')
            ->orderBy('serial')
            ->orderBy('id')
            ->get();

        return view('school.students.configuration.group', compact(
            'classes', 'groups', 'selectedClassId', 'selectedGroupIds', 'assignments'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'group_ids' => ['array'],
            'group_ids.*' => ['exists:groups,id'],
        ]);

        $groupIds = $data['group_ids'] ?? [];

        $existing = ClassGroupAssignment::where('school_class_id', $data['school_class_id'])->get()->keyBy('group_id');

        $existing->whereNotIn('group_id', $groupIds)->each->delete();

        foreach ($groupIds as $groupId) {
            if ($existing->has($groupId)) {
                continue;
            }
            ClassGroupAssignment::create([
                'school_class_id' => $data['school_class_id'],
                'group_id' => $groupId,
            ]);
        }

        return redirect()->route('school.students.configuration.group', ['school_class_id' => $data['school_class_id']])
            ->with('status', 'Group configuration saved.');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'serials' => ['array'],
            'serials.*' => ['nullable', 'integer', 'min:0'],
            'fees' => ['array'],
            'fees.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($data['serials'] ?? [] as $id => $serial) {
            ClassGroupAssignment::whereKey($id)->update(['serial' => $serial ?? 0]);
        }
        foreach ($data['fees'] ?? [] as $id => $fee) {
            ClassGroupAssignment::whereKey($id)->update(['application_fee' => $fee !== '' ? $fee : null]);
        }

        return redirect()->route('school.students.configuration.group')->with('status', 'Group configuration updated.');
    }

    public function toggleStatus(ClassGroupAssignment $classGroupAssignment): RedirectResponse
    {
        $classGroupAssignment->update(['status' => $classGroupAssignment->status === 'enable' ? 'disable' : 'enable']);

        return redirect()->route('school.students.configuration.group');
    }

    public function destroy(ClassGroupAssignment $classGroupAssignment): RedirectResponse
    {
        $classGroupAssignment->delete();

        return redirect()->route('school.students.configuration.group')->with('status', 'Group assignment removed.');
    }
}
