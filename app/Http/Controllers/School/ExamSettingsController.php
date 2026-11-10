<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamShortCode;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamSettingsController extends Controller
{
    public function index(Request $request): View
    {
        $exams = Exam::orderByDesc('starts_on')->get();
        $subjects = Subject::orderBy('code')->orderBy('name')->get();
        $shortCodes = ExamShortCode::orderBy('serial')->get();
        $classes = SchoolClass::orderBy('sort_order')->get();
        $grades = Grade::with('schoolClass')->whereNotNull('school_class_id')->orderBy('school_class_id')->orderByDesc('min_percent')->get();

        $selectedClassId = $request->integer('school_class_id') ?: null;
        $selectedClassGrades = $selectedClassId
            ? Grade::where('school_class_id', $selectedClassId)->pluck('name')->all()
            : [];

        return view('school.exams.settings', compact('exams', 'subjects', 'shortCodes', 'classes', 'grades', 'selectedClassId', 'selectedClassGrades'));
    }
}
