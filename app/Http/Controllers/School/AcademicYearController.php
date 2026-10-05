<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();

        return view('school.academic-years.index', compact('academicYears'));
    }

    public function create(): View
    {
        return view('school.academic-years.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if (! empty($data['is_current'])) {
            AcademicYear::query()->update(['is_current' => false]);
        }

        AcademicYear::create($data);

        return redirect()->route('school.academic-years.index')->with('status', 'Academic year created.');
    }

    public function edit(AcademicYear $academicYear): View
    {
        return view('school.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $data = $this->validated($request);

        if (! empty($data['is_current'])) {
            AcademicYear::query()->update(['is_current' => false]);
        }

        $academicYear->update($data);

        return redirect()->route('school.academic-years.index')->with('status', 'Academic year updated.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->delete();

        return redirect()->route('school.academic-years.index')->with('status', 'Academic year deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after:starts_on'],
            'is_current' => ['sometimes', 'boolean'],
        ]);

        $data['is_current'] = $request->boolean('is_current');

        return $data;
    }
}
