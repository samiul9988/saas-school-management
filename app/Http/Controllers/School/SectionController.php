<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function index(): View
    {
        $sections = Section::with('schoolClass')->withCount('students')->get();

        return view('school.sections.index', compact('sections'));
    }

    public function create(): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();

        return view('school.sections.create', compact('classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        Section::create($this->validated($request));

        return redirect()->route('school.sections.index')->with('status', 'Section created.');
    }

    public function edit(Section $section): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();

        return view('school.sections.edit', compact('section', 'classes'));
    }

    public function update(Request $request, Section $section): RedirectResponse
    {
        $section->update($this->validated($request));

        return redirect()->route('school.sections.index')->with('status', 'Section updated.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();

        return redirect()->route('school.sections.index')->with('status', 'Section deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ]);
    }
}
