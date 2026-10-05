<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::latest()->paginate(15);

        return view('school.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('school.teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Teacher::create($this->validated($request));

        return redirect()->route('school.teachers.index')->with('status', 'Teacher added.');
    }

    public function show(Teacher $teacher): View
    {
        return view('school.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        return view('school.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $teacher->update($this->validated($request));

        return redirect()->route('school.teachers.index')->with('status', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()->route('school.teachers.index')->with('status', 'Teacher removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'designation' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
