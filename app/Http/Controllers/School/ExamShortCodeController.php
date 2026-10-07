<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ExamShortCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExamShortCodeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'integer', 'min:0'],
        ]);

        ExamShortCode::create($data);

        return redirect()->route('school.exams.settings', ['tab' => 'short-code'])->with('status', 'Exam short code added.');
    }

    public function destroy(ExamShortCode $examShortCode): RedirectResponse
    {
        $examShortCode->delete();

        return redirect()->route('school.exams.settings', ['tab' => 'short-code'])->with('status', 'Exam short code removed.');
    }
}
