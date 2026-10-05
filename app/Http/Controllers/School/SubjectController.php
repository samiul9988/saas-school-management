<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::with('schoolClass')->get();

        return view('school.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();

        return view('school.subjects.create', compact('classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        Subject::create($this->validated($request));

        return redirect()->route('school.subjects.index')->with('status', 'Subject created.');
    }

    public function edit(Subject $subject): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();

        return view('school.subjects.edit', compact('subject', 'classes'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $subject->update($this->validated($request));

        return redirect()->route('school.subjects.index')->with('status', 'Subject updated.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()->route('school.subjects.index')->with('status', 'Subject deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
