<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamShortCode;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\View\View;

class ExamSettingsController extends Controller
{
    public function index(): View
    {
        $exams = Exam::orderByDesc('starts_on')->get();
        $subjects = Subject::orderBy('name')->get();
        $shortCodes = ExamShortCode::orderBy('serial')->get();
        $grades = Grade::orderByDesc('min_percent')->get();

        return view('school.exams.settings', compact('exams', 'subjects', 'shortCodes', 'grades'));
    }
}
