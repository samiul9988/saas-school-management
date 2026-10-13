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

    public function hrList(): View
    {
        $teachers = Teacher::latest()->get();

        return view('school.teachers.hr-list', compact('teachers'));
    }

    public function hrUpdate(): View
    {
        $teachers = Teacher::latest()->get();

        return view('school.teachers.update', compact('teachers'));
    }

    public function enableDisable(Request $request): View
    {
        $tab = $request->query('tab') === 'enable' ? 'enable' : 'disable';

        $teachers = Teacher::where('status', $tab === 'disable' ? 'active' : 'inactive')
            ->latest()
            ->get();

        return view('school.teachers.enable-disable', compact('teachers', 'tab'));
    }

    public function enableDisableStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'teacher_ids' => ['required', 'array', 'min:1'],
            'teacher_ids.*' => ['exists:teachers,id'],
            'action' => ['required', 'in:enable,disable'],
        ]);

        Teacher::whereIn('id', $data['teacher_ids'])
            ->update(['status' => $data['action'] === 'enable' ? 'active' : 'inactive']);

        return redirect()->route('school.teachers.enable-disable', ['tab' => $data['action']])
            ->with('status', 'Staff '.($data['action'] === 'enable' ? 'enabled' : 'disabled').'.');
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
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'religion' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string'],
        ]);
    }
}
