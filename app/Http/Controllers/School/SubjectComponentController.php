<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SubjectComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubjectComponentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_short_code_id' => ['required', 'exists:exam_short_codes,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'pass_mark' => ['required', 'numeric', 'min:0'],
            'acceptance' => ['required', 'numeric', 'min:0'],
        ]);

        SubjectComponent::updateOrCreate(
            ['subject_id' => $data['subject_id'], 'exam_short_code_id' => $data['exam_short_code_id']],
            ['total' => $data['total'], 'pass_mark' => $data['pass_mark'], 'acceptance' => $data['acceptance']]
        );

        return back()->with('status', 'Short code added to subject.');
    }

    public function destroy(SubjectComponent $subjectComponent): RedirectResponse
    {
        $subjectComponent->delete();

        return back()->with('status', 'Short code removed from subject.');
    }
}
