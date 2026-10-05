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
