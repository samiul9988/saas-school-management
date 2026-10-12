<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Shift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassConfigurationController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::with('sections')->orderBy('sort_order')->get();
        $shifts = Shift::orderBy('serial')->get();

        $perPage = (int) $request->get('per_page', 10);
        $sections = Section::with('schoolClass', 'shift')
            ->orderBy('serial')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('school.students.configuration.class', compact('classes', 'shifts', 'sections', 'perPage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
        ]);

        $section = Section::where('id', $data['section_id'])->where('school_class_id', $data['school_class_id'])->firstOrFail();
        $section->update(['shift_id' => $data['shift_id'] ?? null]);

        return redirect()->route('school.students.configuration.class')->with('status', 'Class configuration saved.');
    }

    public function updateSerial(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'serials' => ['required', 'array'],
            'serials.*' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($data['serials'] as $sectionId => $serial) {
            Section::whereKey($sectionId)->update(['serial' => $serial ?? 0]);
        }

        return redirect()->route('school.students.configuration.class')->with('status', 'Serial updated.');
    }

    public function toggleStatus(Section $section): RedirectResponse
    {
        $section->update(['status' => $section->status === 'enable' ? 'disable' : 'enable']);

        return redirect()->route('school.students.configuration.class');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->update(['shift_id' => null]);

        return redirect()->route('school.students.configuration.class')->with('status', 'Shift assignment removed.');
    }
}
