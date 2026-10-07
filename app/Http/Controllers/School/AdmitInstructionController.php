<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AdmitInstruction;
use App\Models\Exam;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdmitInstructionController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $exams = Exam::orderByDesc('starts_on')->get();

        $instruction = null;
        if ($request->filled('school_class_id') && $request->filled('exam_id')) {
            $instruction = AdmitInstruction::firstOrNew([
                'school_class_id' => $request->school_class_id,
                'exam_id' => $request->exam_id,
            ]);
        }

        return view('school.exams.admit-instruction', compact('classes', 'exams', 'instruction'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'instruction_1' => ['nullable', 'string', 'max:255'],
            'instruction_2' => ['nullable', 'string', 'max:255'],
            'instruction_3' => ['nullable', 'string', 'max:255'],
            'instruction_4' => ['nullable', 'string', 'max:255'],
        ]);

        AdmitInstruction::updateOrCreate(
            ['school_class_id' => $data['school_class_id'], 'exam_id' => $data['exam_id']],
            $data
        );

        return redirect()->route('school.exams.admit-instruction', [
            'school_class_id' => $data['school_class_id'],
            'exam_id' => $data['exam_id'],
        ])->with('status', 'Admit card instructions saved.');
    }
}
