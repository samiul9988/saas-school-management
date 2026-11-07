<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    /**
     * Distinct, non-empty group names actually assigned to this class's students,
     * so Group dropdowns reflect real data instead of a fixed list.
     */
    public function groups(SchoolClass $class): JsonResponse
    {
        $groups = Student::where('school_class_id', $class->id)
            ->whereNotNull('group_name')
            ->where('group_name', '!=', '')
            ->distinct()
            ->orderBy('group_name')
            ->pluck('group_name');

        return response()->json(['groups' => $groups]);
    }

    public function index(): View
    {
        $classes = SchoolClass::withCount('students')->orderBy('sort_order')->get();

        return view('school.classes.index', compact('classes'));
    }

    public function create(): View
    {
        return view('school.classes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        SchoolClass::create($this->validated($request));

        return redirect()->route('school.classes.index')->with('status', 'Class created.');
    }

    public function edit(SchoolClass $class): View
    {
        return view('school.classes.edit', ['schoolClass' => $class]);
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $class->update($this->validated($request));

        return redirect()->route('school.classes.index')->with('status', 'Class updated.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('school.classes.index')->with('status', 'Class deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);
    }
}
