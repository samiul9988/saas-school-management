<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(): View
    {
        $grades = Grade::orderByDesc('min_percent')->get();

        return view('school.grades.index', compact('grades'));
    }

    public function create(): View
    {
        return view('school.grades.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Grade::create($this->validated($request));

        return redirect()->route('school.grades.index')->with('status', 'Grade added.');
    }

    public function edit(Grade $grade): View
    {
        return view('school.grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade): RedirectResponse
    {
        $grade->update($this->validated($request));

        return redirect()->route('school.grades.index')->with('status', 'Grade updated.');
    }

    public function destroy(Grade $grade): RedirectResponse
    {
        $grade->delete();

        return redirect()->route('school.grades.index')->with('status', 'Grade deleted.');
    }

    /**
     * Bulk-sets a class's grading scale from the fixed A+..F standard bands:
     * checked grades are created/updated for the class, unchecked ones are
     * removed, so the class's scale always matches the current selection.
     */
    public function classSetup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'grades' => ['array'],
            'grades.*' => ['in:'.implode(',', array_keys(Grade::STANDARD_GRADES))],
        ]);

        $selected = $data['grades'] ?? [];

        foreach (Grade::STANDARD_GRADES as $name => $band) {
            if (in_array($name, $selected, true)) {
                Grade::updateOrCreate(
                    ['school_class_id' => $data['school_class_id'], 'name' => $name],
                    ['min_percent' => $band['min'], 'max_percent' => $band['max'], 'grade_point' => $band['point']]
                );
            } else {
                Grade::where('school_class_id', $data['school_class_id'])->where('name', $name)->delete();
            }
        }

        return redirect()->route('school.exams.settings', ['tab' => 'grade', 'school_class_id' => $data['school_class_id']])
            ->with('status', 'Grade setup saved.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'min_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'max_percent' => ['required', 'numeric', 'min:0', 'max:100', 'gte:min_percent'],
            'grade_point' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
